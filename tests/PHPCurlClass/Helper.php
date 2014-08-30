<?php
namespace Helper;

use Curl\Curl;

class Test
{
    const TEST_URL = 'http://127.0.0.1:8000/';
    const ERROR_URL = 'https://1.2.3.4/';

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }

    public function server($test, $request_method, $data = array())
    {
        $this->curl->setHeader('X-DEBUG-TEST', $test);
        $request_method = strtolower($request_method);
        $this->curl->$request_method(self::TEST_URL, $data);
        return $this->curl->response;
    }
}

function test($instance, $before, $after)
{
    $instance->server('request_method', $before);
    \PHPUnit_Framework_Assert::assertEquals($before, $instance->curl->response_headers['X-REQUEST-METHOD']);
    $instance->server('request_method', $after);
    \PHPUnit_Framework_Assert::assertEquals($after, $instance->curl->response_headers['X-REQUEST-METHOD']);
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

function get_png()
{
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, create_png());
    return $tmp_filename;
}
