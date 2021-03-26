<?php

namespace Helper;

use Curl\Curl;

class Test
{
    const TEST_URL = 'http://127.0.0.1:8000/';
    const ERROR_URL = 'http://1.2.3.4/';

    public $message;

    private $testUrl;

    public function __construct($port = null)
    {
        $this->testUrl = $port === null ? self::TEST_URL : $this->getTestUrl($port);
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }

    public function server($test, $request_method, $arg1 = null, $arg2 = null)
    {
        $this->curl->setHeader('X-DEBUG-TEST', $test);
        $request_method = strtolower($request_method);
        if ($arg1 !== null && $arg2 !== null) {
            $this->curl->$request_method($this->testUrl, $arg1, $arg2);
        } elseif ($arg1 !== null) {
            $this->curl->$request_method($this->testUrl, $arg1);
        } else {
            $this->curl->$request_method($this->testUrl);
        }

        $this->message =
            'error message: ' . $this->curl->errorMessage . "\n" .
            'curl error message: ' . $this->curl->curlErrorMessage . "\n" .
            'http error message: ' . $this->curl->httpErrorMessage . "\n" .
            'error code: ' . $this->curl->errorCode . "\n" .
            'curl error code: ' . $this->curl->curlErrorCode . "\n" .
            'raw response: ' . $this->curl->rawResponse . "\n" .
            '';

        return $this->curl->response;
    }

    /*
     * When chaining requests, the method must be forced, otherwise a
     * previously forced method might be inherited.
     * Especially, POSTs must be configured to not perform post-redirect-get.
     */
    private function chainedRequest($request_method, $data)
    {
        if ($request_method === 'POST') {
            $this->server('request_method', $request_method, $data, true);
        } else {
            $this->server('request_method', $request_method, $data);
        }
        \PHPUnit\Framework\Assert::assertEquals($request_method, $this->curl->responseHeaders['X-REQUEST-METHOD']);
    }

    public function chainRequests($first, $second, $data = [])
    {
        $this->chainedRequest($first, $data);
        $this->chainedRequest($second, $data);
    }

    public static function getTestUrl($port)
    {
        // Return url pointing to a test server running on the specified port.
        // To avoid installing and configuring a web server for the tests, PHP's
        // built-in development server is used. As each development server can
        // only handle one request at a time (single-threaded) and some tests
        // expect the server to handle requests simultaneously, multiple
        // instances are run on different ports. With this setup, requests in
        // the test can be made to the various port urls without having to be
        // handled sequentially.
        return 'http://127.0.0.1:' . $port . '/';
    }
}

function create_png()
{
    // PNG image data, 1 x 1, 1-bit colormap, non-interlaced
    ob_start();
    imagepng(imagecreatefromstring(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')));
    $raw_image = ob_get_contents();
    ob_end_clean();
    return $raw_image;
}

function create_tmp_file($data)
{
    $tmp_file = tmpfile();
    fwrite($tmp_file, $data);
    rewind($tmp_file);
    return $tmp_file;
}

function get_tmp_file_path()
{
    // Return temporary file path without creating file.
    $tmp_file_path =
        rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) .
        DIRECTORY_SEPARATOR . 'php-curl-class.' . uniqid(rand(), true);
    return $tmp_file_path;
}

function get_png()
{
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, create_png());
    return $tmp_filename;
}

if (function_exists('finfo_open')) {
    function mime_type($file_path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime_type;
    }
} else {
    function mime_type($file_path)
    {
        $mime_type = mime_content_type($file_path);
        return $mime_type;
    }
}

function upload_file_to_server($upload_file_path) {
    $upload_test = new Test();
    $upload_test->server('upload_response', 'POST', [
        'image' => '@' . $upload_file_path,
    ]);
    $uploaded_file_path = $upload_test->curl->response->file_path;

    // Ensure files are not the same path.
    assert($upload_file_path !== $uploaded_file_path);

    // Ensure file uploaded successfully.
    assert(md5_file($upload_file_path) === $upload_test->curl->responseHeaders['ETag']);

    return $uploaded_file_path;
}

function remove_file_from_server($uploaded_file_path) {
    $download_test = new Test();

    // Ensure file successfully removed.
    assert('true' === $download_test->server('upload_cleanup', 'POST', [
        'file_path' => $uploaded_file_path,
    ]));
    assert(file_exists($uploaded_file_path) === false);
}

function get_curl_property_value($instance, $property_name)
{
    $reflector = new \ReflectionClass('\Curl\Curl');
    $property = $reflector->getProperty($property_name);
    $property->setAccessible(true);
    return $property->getValue($instance);
}

function get_multi_curl_property_value($instance, $property_name)
{
    $reflector = new \ReflectionClass('\Curl\MultiCurl');
    $property = $reflector->getProperty($property_name);
    $property->setAccessible(true);
    return $property->getValue($instance);
}

function get_request_stats($request_stats, $multi_curl)
{
    $messages =
        ['total duration: ' . sprintf('%.6f', round($multi_curl->stopTime - $multi_curl->startTime, 6)) . "\n"];

    foreach ($request_stats as $instance_id => &$value) {
        $value['relative_start'] = sprintf('%.6f', round($value['start'] - $multi_curl->startTime, 6));
        $value['relative_stop'] = sprintf('%.6f', round($value['stop'] - $multi_curl->startTime, 6));
        $value['duration'] = (string)round($value['stop'] - $value['start'], 6);

        $messages[] =
            $value['relative_start']        . ' - ' . 'request ' . $instance_id . ' start'        . "\n" .
            $value['relative_stop']         . ' - ' . 'request ' . $instance_id . ' complete'     . "\n" .
            $value['duration']              . ' - ' . 'request ' . $instance_id . ' duration'     . "\n";

        unset($value['start']);
        unset($value['stop']);
    }

    $request_stats['message'] = implode("\n", $messages);

    return $request_stats;
}
