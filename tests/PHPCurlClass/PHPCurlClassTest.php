<?php
require '../src/Curl/Curl.php';
require 'Helper.php';

use \Curl\Curl;
use \Curl\CaseInsensitiveArray;
use \Helper\Test;

class CurlTest extends PHPUnit_Framework_TestCase
{
    public function testExtensionsLoaded()
    {
        $this->assertTrue(extension_loaded('curl'));
        $this->assertTrue(extension_loaded('gd'));
    }

    public function testArrayAssociative()
    {
        $this->assertTrue(Curl::is_array_assoc(array(
            'foo' => 'wibble',
            'bar' => 'wubble',
            'baz' => 'wobble',
        )));
    }

    public function testArrayIndexed()
    {
        $this->assertFalse(Curl::is_array_assoc(array(
            'wibble',
            'wubble',
            'wobble',
        )));
    }

    public function testCaseInsensitiveArrayGet()
    {
        $array = new CaseInsensitiveArray();
        $this->assertTrue(is_object($array));
        $this->assertCount(0, $array);
        $this->assertNull($array[(string)rand()]);

        $array['foo'] = 'bar';
        $this->assertNotEmpty($array);
        $this->assertCount(1, $array);
    }

    public function testCaseInsensitiveArraySet()
    {
        $array = new CaseInsensitiveArray();
        foreach (array('FOO', 'FOo', 'Foo', 'fOO', 'fOo', 'foO', 'foo') as $key) {
            $value = mt_rand();
            $array[$key] = $value;
            $this->assertCount(1, $array);
            $this->assertEquals($value, $array['FOO']);
            $this->assertEquals($value, $array['FOo']);
            $this->assertEquals($value, $array['Foo']);
            $this->assertEquals($value, $array['fOO']);
            $this->assertEquals($value, $array['fOo']);
            $this->assertEquals($value, $array['foO']);
            $this->assertEquals($value, $array['foo']);
        }

        $array['baz'] = 'qux';
        $this->assertCount(2, $array);
    }

    public function testUserAgent()
    {
        $php_version = 'PHP\/' . PHP_VERSION;
        $curl_version = curl_version();
        $curl_version = 'curl\/' . $curl_version['version'];

        $test = new Test();
        $user_agent = $test->server('server', 'GET', array('key' => 'HTTP_USER_AGENT'));
        $this->assertRegExp('/' . $php_version . '/', $user_agent);
        $this->assertRegExp('/' . $curl_version . '/', $user_agent);
    }

    public function testGet()
    {
        $test = new Test();
        $this->assertEquals('GET', $test->server('server', 'GET', array(
            'key' => 'REQUEST_METHOD',
        )));
    }

    public function testUrl()
    {
        $data = array('foo' => 'bar');

        // curl -v --get --request GET "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'GET', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);

        // curl -v --request POST "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'POST', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL, $test->curl->url);

        // curl -v --request PUT "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'PUT', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL, $test->curl->url);

        // curl -v --request PATCH "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'PATCH', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL, $test->curl->url);

        // curl -v --get --request DELETE "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'DELETE', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);

        // curl -v --get --request HEAD --head "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'HEAD', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);

        // curl -v --get --request OPTIONS "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'OPTIONS', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->base_url);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);
    }

    public function testPostRequestMethod()
    {
        $test = new Test();
        $this->assertEquals('POST', $test->server('server', 'POST', array(
            'key' => 'REQUEST_METHOD',
        )));
    }

    public function testPostContinueResponseHeader()
    {
        // 100 Continue responses may contain additional optional headers per
        // RFC 2616, Section 10.1:
        // This class of status code indicates a provisional response,
        // consisting only of the Status-Line and optional headers, and is
        // terminated by an empty line.
        $response =
            'HTTP/1.1 100 Continue' . "\r\n" .
            'Date: Fri, 01 Jan 1990 00:00:00 GMT' . "\r\n" .
            'Server: PHP-Curl-Class' . "\r\n" .
            "\r\n" .
            'HTTP/1.1 200 OK' . "\r\n" .
            'Date: Fri, 01 Jan 1990 00:00:00 GMT' . "\r\n" .
            'Cache-Control: private' . "\r\n" .
            'Vary: Accept-Encoding' . "\r\n" .
            'Content-Length: 2' . "\r\n" .
            'Content-Type: text/plain;charset=UTF-8' . "\r\n" .
            'Server: PHP-Curl-Class' . "\r\n" .
            'Connection: keep-alive' . "\r\n" .
            "\r\n";

        $reflector = new ReflectionClass('\Curl\Curl');
        $reflection_method = $reflector->getMethod('parseResponseHeaders');
        $reflection_method->setAccessible(true);

        $curl = new Curl();
        $response_headers = $reflection_method->invoke($curl, $response);

        $this->assertEquals('HTTP/1.1 200 OK', $response_headers['Status-Line']);
    }

    public function testPostData()
    {
        $test = new Test();
        $this->assertEquals('key=value', $test->server('post', 'POST', array(
            'key' => 'value',
        )));
    }

    public function testPostAssociativeArrayData()
    {
        $test = new Test();
        $this->assertEquals(
            'username=myusername' .
            '&password=mypassword' .
            '&more_data%5Bparam1%5D=something' .
            '&more_data%5Bparam2%5D=other%20thing' .
            '&more_data%5Bparam3%5D=123' .
            '&more_data%5Bparam4%5D=3.14',
            $test->server('post_multidimensional', 'POST', array(
                'username' => 'myusername',
                'password' => 'mypassword',
                'more_data' => array(
                    'param1' => 'something',
                    'param2' => 'other thing',
                    'param3' => 123,
                    'param4' => 3.14,
                ),
            ))
        );
    }

    public function testPostMultidimensionalData()
    {
        $test = new Test();
        $this->assertEquals(
            'key=file&file%5B%5D=wibble&file%5B%5D=wubble&file%5B%5D=wobble',
            $test->server('post_multidimensional', 'POST', array(
                'key' => 'file',
                'file' => array(
                    'wibble',
                    'wubble',
                    'wobble',
                ),
            ))
        );
    }

    public function testPostFilePathUpload()
    {
        $file_path = Helper\get_png();

        $test = new Test();
        $this->assertEquals('image/png', $test->server('post_file_path_upload', 'POST', array(
            'key' => 'image',
            'image' => '@' . $file_path,
        )));

        unlink($file_path);
        $this->assertFalse(file_exists($file_path));
    }

    public function testPostCurlFileUpload()
    {
        if (class_exists('CURLFile')) {
            $file_path = Helper\get_png();

            $test = new Test();
            $this->assertEquals('image/png', $test->server('post_file_path_upload', 'POST', array(
                'key' => 'image',
                'image' => new CURLFile($file_path),
            )));

            unlink($file_path);
            $this->assertFalse(file_exists($file_path));
        }
    }

    public function testPutRequestMethod()
    {
        $test = new Test();
        $this->assertEquals('PUT', $test->server('request_method', 'PUT'));
    }

    public function testPutData()
    {
        $test = new Test();
        $this->assertEquals('key=value', $test->server('put', 'PUT', array(
            'key' => 'value',
        )));

        $test = new Test();
        $this->assertEquals('{"key":"value"}', $test->server('put', 'PUT', json_encode(array(
            'key' => 'value',
        ))));
    }

    public function testPutFileHandle()
    {
        $png = Helper\create_png();
        $tmp_file = Helper\create_tmp_file($png);

        $test = new Test();
        $test->curl->setHeader('X-DEBUG-TEST', 'put_file_handle');
        $test->curl->setOpt(CURLOPT_PUT, true);
        $test->curl->setOpt(CURLOPT_INFILE, $tmp_file);
        $test->curl->setOpt(CURLOPT_INFILESIZE, strlen($png));
        $test->curl->put(Test::TEST_URL);

        fclose($tmp_file);

        $this->assertEquals('image/png', $test->curl->response);
    }

    public function testPatchRequestMethod()
    {
        $test = new Test();
        $this->assertEquals('PATCH', $test->server('request_method', 'PATCH'));
    }

    public function testDelete()
    {
        $test = new Test();
        $this->assertEquals('DELETE', $test->server('server', 'DELETE', array(
            'key' => 'REQUEST_METHOD',
        )));

        $test = new Test();
        $this->assertEquals('delete', $test->server('delete', 'DELETE', array(
            'test' => 'delete',
            'key' => 'test',
        )));
    }

    public function testHeadRequestMethod()
    {
        $test = new Test();
        $test->server('request_method', 'HEAD', array(
            'key' => 'REQUEST_METHOD',
        ));
        $this->assertEquals('HEAD', $test->curl->response_headers['X-REQUEST-METHOD']);
        $this->assertEmpty($test->curl->response);
    }

    public function testOptionsRequestMethod()
    {
        $test = new Test();
        $test->server('request_method', 'OPTIONS', array(
            'key' => 'REQUEST_METHOD',
        ));
        $this->assertEquals('OPTIONS', $test->curl->response_headers['X-REQUEST-METHOD']);
    }

    public function testDownload()
    {
        // Upload a file.
        $upload_file_path = Helper\get_png();
        $upload_test = new Test();
        $upload_test->server('upload_response', 'POST', array(
            'image' => '@' . $upload_file_path,
        ));
        $uploaded_file_path = $upload_test->curl->response->file_path;
        $this->assertNotEquals($upload_file_path, $uploaded_file_path);
        $this->assertEquals(md5_file($upload_file_path), $upload_test->curl->response_headers['ETag']);

        // Download the file.
        $downloaded_file_path = tempnam('/tmp', 'php-curl-class.');
        $download_test = new Test();
        $download_test->curl->setHeader('X-DEBUG-TEST', 'download_response');
        $this->assertTrue($download_test->curl->download(Test::TEST_URL . '?' . http_build_query(array(
            'file_path' => $uploaded_file_path,
        )), $downloaded_file_path));
        $this->assertNotEquals($uploaded_file_path, $downloaded_file_path);

        $this->assertEquals(filesize($upload_file_path), filesize($downloaded_file_path));
        $this->assertEquals(md5_file($upload_file_path), md5_file($downloaded_file_path));
        $this->assertEquals(md5_file($upload_file_path), $download_test->curl->response_headers['ETag']);

        // Ensure successive requests set the appropriate values.
        $this->assertEquals('GET', $download_test->server('server', 'GET', array(
            'key' => 'REQUEST_METHOD',
        )));
        $this->assertFalse(is_bool($download_test->curl->response));
        $this->assertFalse(is_bool($download_test->curl->raw_response));

        // Remove server file.
        $this->assertEquals('true', $download_test->server('upload_cleanup', 'POST', array(
            'file_path' => $uploaded_file_path,
        )));

        unlink($upload_file_path);
        unlink($downloaded_file_path);
        $this->assertFalse(file_exists($upload_file_path));
        $this->assertFalse(file_exists($uploaded_file_path));
        $this->assertFalse(file_exists($downloaded_file_path));
    }

    public function testBasicHttpAuth()
    {
        $test = new Test();
        $this->assertEquals('canceled', $test->server('http_basic_auth', 'GET'));

        $username = 'myusername';
        $password = 'mypassword';
        $test = new Test();
        $test->curl->setBasicAuthentication($username, $password);
        $test->server('http_basic_auth', 'GET');
        $json = $test->curl->response;
        $this->assertEquals($username, $json->username);
        $this->assertEquals($password, $json->password);
    }

    public function testDigestHttpAuth()
    {
        $username = 'myusername';
        $password = 'mypassword';
        $invalid_password = 'anotherpassword';

        $test = new Test();
        $test->server('http_digest_auth', 'GET');
        $this->assertEquals('canceled', $test->curl->response);
        $this->assertEquals(401, $test->curl->http_status_code);

        $test = new Test();
        $test->curl->setDigestAuthentication($username, $invalid_password);
        $test->server('http_digest_auth', 'GET');
        $this->assertEquals('invalid', $test->curl->response);
        $this->assertEquals(401, $test->curl->http_status_code);

        $test = new Test();
        $test->curl->setDigestAuthentication($username, $password);
        $test->server('http_digest_auth', 'GET');
        $this->assertEquals('valid', $test->curl->response);
        $this->assertEquals(200, $test->curl->http_status_code);
    }

    public function testReferrer()
    {
        $test = new Test();
        $test->curl->setReferrer('myreferrer');
        $this->assertEquals('myreferrer', $test->server('server', 'GET', array(
            'key' => 'HTTP_REFERER',
        )));

        $test = new Test();
        $test->curl->setReferer('myreferer');
        $this->assertEquals('myreferer', $test->server('server', 'GET', array(
            'key' => 'HTTP_REFERER',
        )));
    }

    public function testResponseBody()
    {
        foreach (array(
            'GET' => 'OK',
            'POST' => 'OK',
            'PUT' => 'OK',
            'PATCH' => 'OK',
            'DELETE' => 'OK',
            'HEAD' => '',
            'OPTIONS' => 'OK',
            ) as $request_method => $expected_response) {
            $curl = new Curl();
            $curl->setHeader('X-DEBUG-TEST', 'response_body');
            $this->assertEquals($expected_response, $curl->$request_method(Test::TEST_URL));
        }
    }

    public function testCookies()
    {
        $test = new Test();
        $test->curl->setCookie('mycookie', 'yum');
        $this->assertEquals('yum', $test->server('cookie', 'GET', array(
            'key' => 'mycookie',
        )));
    }

    public function testCookieEncoding()
    {
        $curl = new Curl();
        $curl->setCookie('cookie', 'Om nom nom nom');

        $reflectionClass = new ReflectionClass('\Curl\Curl');
        $reflectionProperty = $reflectionClass->getProperty('options');
        $reflectionProperty->setAccessible(true);
        $options = $reflectionProperty->getValue($curl);
        $this->assertEquals('cookie=Om%20nom%20nom%20nom', $options[CURLOPT_COOKIE]);
    }

    public function testCookieFile()
    {
        $cookie_file = dirname(__FILE__) . '/cookies.txt';
        $cookie_data = implode("\t", array(
            '127.0.0.1', // domain
            'FALSE',     // tailmatch
            '/',         // path
            'FALSE',     // secure
            '0',         // expires
            'mycookie',  // name
            'yum',       // value
        ));
        file_put_contents($cookie_file, $cookie_data);

        $test = new Test();
        $test->curl->setCookieFile($cookie_file);
        $this->assertEquals('yum', $test->server('cookie', 'GET', array(
            'key' => 'mycookie',
        )));

        unlink($cookie_file);
        $this->assertFalse(file_exists($cookie_file));
    }

    public function testCookieJar()
    {
        $cookie_file = dirname(__FILE__) . '/cookies.txt';

        $test = new Test();
        $test->curl->setCookieJar($cookie_file);
        $test->server('cookiejar', 'GET');
        $test->curl->close();

        $this->assertTrue(!(strpos(file_get_contents($cookie_file), "\t" . 'mycookie' . "\t" . 'yum') === false));
        unlink($cookie_file);
        $this->assertFalse(file_exists($cookie_file));
    }

    public function testMultipleCookieResponse()
    {
        $test = new Test();
        $test->server('multiple_cookie', 'GET');
        $this->assertEquals('cookie1=scrumptious,cookie2=mouthwatering', $test->curl->response_headers['Set-Cookie']);
    }

    public function testDefaultTimeout() {
        $test = new Test();
        $test->server('timeout', 'GET', array(
            'seconds' => '31',
        ));
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curl_error);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->error_code);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curl_error_code);
        $this->assertFalse($test->curl->http_error);
    }

    public function testTimeoutError() {
        $test = new Test();
        $test->curl->setTimeout(5);
        $test->server('timeout', 'GET', array(
            'seconds' => '10',
        ));
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curl_error);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->error_code);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curl_error_code);
        $this->assertFalse($test->curl->http_error);
    }

    public function testTimeout() {
        $test = new Test();
        $test->curl->setTimeout(10);
        $test->server('timeout', 'GET', array(
            'seconds' => '5',
        ));
        $this->assertFalse($test->curl->error);
        $this->assertFalse($test->curl->curl_error);
        $this->assertNotEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->error_code);
        $this->assertNotEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curl_error_code);
        $this->assertFalse($test->curl->http_error);
    }

    public function testError()
    {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 4000);
        $test->curl->get(Test::ERROR_URL);
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curl_error);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->error_code);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curl_error_code);
    }

    public function testErrorMessage()
    {
        $test = new Test();
        $test->server('error_message', 'GET');
        $this->assertEquals('HTTP/1.1 401 Unauthorized', $test->curl->error_message);
    }

    public function testRequestHeaderCaseSensitivity()
    {
        $content_type = 'application/json';
        $curl = new Curl();
        $curl->setHeader('Content-Type', $content_type);

        $reflector = new ReflectionClass('\Curl\Curl');
        $property = $reflector->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($curl);

        $this->assertEquals($content_type, $headers['Content-Type']);
        $this->assertEquals($content_type, $headers['content-type']);
        $this->assertEquals($content_type, $headers['CONTENT-TYPE']);
        $this->assertEquals($content_type, $headers['cOnTeNt-TyPe']);
    }

    public function testResponseHeaders()
    {
        $test = new Test();
        $test->curl->setHeader('Content-Type', 'application/json');
        $test->curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $test->curl->setHeader('Accept', 'application/json');
        $this->assertEquals('application/json', $test->server('server', 'GET', array('key' => 'CONTENT_TYPE')));
        $this->assertEquals('XMLHttpRequest', $test->server('server', 'GET', array('key' => 'HTTP_X_REQUESTED_WITH')));
        $this->assertEquals('application/json', $test->server('server', 'GET', array('key' => 'HTTP_ACCEPT')));
    }

    public function testResponseHeaderCaseSensitivity()
    {
        $content_type = 'application/json';
        $test = new Test();
        $test->curl->setHeader('Content-Type', $content_type);
        $test->server('response_header', 'GET');

        $request_headers = $test->curl->request_headers;
        $response_headers = $test->curl->response_headers;

        $this->assertEquals($content_type, $request_headers['Content-Type']);
        $this->assertEquals($content_type, $request_headers['content-type']);
        $this->assertEquals($content_type, $request_headers['CONTENT-TYPE']);
        $this->assertEquals($content_type, $request_headers['cOnTeNt-TyPe']);

        $etag = $response_headers['ETag'];
        $this->assertEquals($etag, $response_headers['ETAG']);
        $this->assertEquals($etag, $response_headers['etag']);
        $this->assertEquals($etag, $response_headers['eTAG']);
        $this->assertEquals($etag, $response_headers['eTaG']);
    }

    public function testHeaderRedirect()
    {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $test->server('redirect', 'GET');
        $this->assertEquals('OK', $test->curl->response);
    }

    public function testRequestURL()
    {
        $test = new Test();
        $this->assertFalse(substr($test->server('request_uri', 'GET'), -1) === '?');
        $test = new Test();
        $this->assertFalse(substr($test->server('request_uri', 'POST'), -1) === '?');
        $test = new Test();
        $this->assertFalse(substr($test->server('request_uri', 'PUT'), -1) === '?');
        $test = new Test();
        $this->assertFalse(substr($test->server('request_uri', 'PATCH'), -1) === '?');
        $test = new Test();
        $this->assertFalse(substr($test->server('request_uri', 'DELETE'), -1) === '?');
    }

    public function testNestedData()
    {
        $test = new Test();
        $data = array(
            'username' => 'myusername',
            'password' => 'mypassword',
            'more_data' => array(
                'param1' => 'something',
                'param2' => 'other thing',
                'another' => array(
                    'extra' => 'level',
                    'because' => 'I need it',
                ),
            ),
        );
        $this->assertEquals(http_build_query($data), $test->server('post', 'POST', $data));
    }

    public function testPostStringUrlEncodedContentType()
    {
        $test = new Test();
        $test->server('server', 'POST', 'foo=bar');
        $this->assertEquals('application/x-www-form-urlencoded', $test->curl->request_headers['Content-Type']);
    }

    public function testPostArrayUrlEncodedContentType()
    {
        $test = new Test();
        $test->server('server', 'POST', array(
            'foo' => 'bar',
        ));
        $this->assertEquals('application/x-www-form-urlencoded', $test->curl->request_headers['Content-Type']);
    }

    public function testPostFileFormDataContentType()
    {
        $file_path = Helper\get_png();

        $test = new Test();
        $test->server('server', 'POST', array(
            'image' => '@' . $file_path,
        ));
        $this->assertEquals('100-continue', $test->curl->request_headers['Expect']);
        preg_match('/^multipart\/form-data; boundary=/', $test->curl->request_headers['Content-Type'], $content_type);
        $this->assertTrue(!empty($content_type));

        unlink($file_path);
        $this->assertFalse(file_exists($file_path));
    }

    public function testPostCurlFileFormDataContentType()
    {
        if (!class_exists('CURLFile')) {
            return;
        }

        $file_path = Helper\get_png();

        $test = new Test();
        $test->server('server', 'POST', array(
            'image' => new CURLFile($file_path),
        ));
        $this->assertEquals('100-continue', $test->curl->request_headers['Expect']);
        preg_match('/^multipart\/form-data; boundary=/', $test->curl->request_headers['Content-Type'], $content_type);
        $this->assertTrue(!empty($content_type));

        unlink($file_path) ;
        $this->assertFalse(file_exists($file_path));
    }

    public function testJSONRequest()
    {
        foreach (
            array(
                array(
                    array(
                        'key' => 'value',
                    ),
                    '{"key":"value"}',
                ),
                array(
                    array(
                        'key' => 'value',
                        'strings' => array(
                            'a',
                            'b',
                            'c',
                        ),
                    ),
                    '{"key":"value","strings":["a","b","c"]}',
                ),
            ) as $test) {
            list($data, $expected_response) = $test;

            $test = new Test();
            $this->assertEquals($expected_response, $test->server('post_json', 'POST', json_encode($data)));

            foreach (array(
                'Content-Type',
                'content-type',
                'CONTENT-TYPE') as $key) {
                foreach (array(
                    'APPLICATION/JSON',
                    'APPLICATION/JSON; CHARSET=UTF-8',
                    'APPLICATION/JSON;CHARSET=UTF-8',
                    'application/json',
                    'application/json; charset=utf-8',
                    'application/json;charset=UTF-8',
                    ) as $value) {
                    $test = new Test();
                    $test->curl->setHeader($key, $value);
                    $this->assertEquals($expected_response, $test->server('post_json', 'POST', json_encode($data)));

                    $test = new Test();
                    $test->curl->setHeader($key, $value);
                    $this->assertEquals($expected_response, $test->server('post_json', 'POST', $data));
                }
            }
        }
    }

    public function testJSONResponse()
    {
        foreach (array(
            'Content-Type',
            'content-type',
            'CONTENT-TYPE') as $key) {
            foreach (array(
                'APPLICATION/JSON',
                'APPLICATION/JSON; CHARSET=UTF-8',
                'APPLICATION/JSON;CHARSET=UTF-8',
                'application/json',
                'application/json; charset=utf-8',
                'application/json;charset=UTF-8',
                ) as $value) {
                $test = new Test();
                $test->server('json_response', 'POST', array(
                    'key' => $key,
                    'value' => $value,
                ));

                $response = $test->curl->response;
                $this->assertNotNull($response);
                $this->assertNull($response->null);
                $this->assertTrue($response->true);
                $this->assertFalse($response->false);
                $this->assertTrue(is_int($response->integer));
                $this->assertTrue(is_float($response->float));
                $this->assertEmpty($response->empty);
                $this->assertTrue(is_string($response->string));
                $this->assertEquals(json_encode(array(
                    'null' => null,
                    'true' => true,
                    'false' => false,
                    'integer' => 1,
                    'float' => 3.14,
                    'empty' => '',
                    'string' => 'string',
                )), $test->curl->raw_response);
            }
        }
    }

    public function testJSONDecoder()
    {
        $data = array(
            'key' => 'Content-Type',
            'value' => 'application/json',
        );

        $test = new Test();
        $test->server('json_response', 'POST', $data);
        $this->assertTrue(is_object($test->curl->response));
        $this->assertFalse(is_array($test->curl->response));

        $test = new Test();
        $test->curl->setJsonDecoder(function($response) {
            return json_decode($response, true);
        });
        $test->server('json_response', 'POST', $data);
        $this->assertFalse(is_object($test->curl->response));
        $this->assertTrue(is_array($test->curl->response));
    }

    public function testXMLResponse()
    {
        foreach (array(
            'Content-Type',
            'content-type',
            'CONTENT-TYPE') as $key) {
            foreach (array(
                'application/atom+xml; charset=UTF-8',
                'application/atom+xml;charset=UTF-8',
                'application/rss+xml',
                'application/rss+xml; charset=utf-8',
                'application/rss+xml;charset=utf-8',
                'application/xml',
                'application/xml; charset=utf-8',
                'application/xml;charset=utf-8',
                'text/xml',
                'text/xml; charset=utf-8',
                'text/xml;charset=utf-8',
                ) as $value) {
                $test = new Test();
                $test->server('xml_response', 'POST', array(
                    'key' => $key,
                    'value' => $value,
                ));

                $this->assertInstanceOf('SimpleXMLElement', $test->curl->response);

                $doc = new DOMDocument();
                $doc->formatOutput = true;
                $rss = $doc->appendChild($doc->createElement('rss'));
                $rss->setAttribute('version', '2.0');
                $channel = $doc->createElement('channel');
                $title = $doc->createElement('title');
                $title->appendChild($doc->createTextNode('Title'));
                $channel->appendChild($title);
                $link = $doc->createElement('link');
                $link->appendChild($doc->createTextNode('Link'));
                $channel->appendChild($link);
                $description = $doc->createElement('description');
                $description->appendChild($doc->createTextNode('Description'));
                $channel->appendChild($description);
                $rss->appendChild($channel);
                $xml = $doc->saveXML();
                $this->assertEquals($xml, $test->curl->raw_response);
            }
        }
    }

    public function testEmptyResponse()
    {
        $response = "\r\n\r\n";

        $reflector = new ReflectionClass('\Curl\Curl');
        $reflection_method = $reflector->getMethod('parseResponseHeaders');
        $reflection_method->setAccessible(true);

        $curl = new Curl();
        $reflection_method->invoke($curl, $response);
    }

    public function testArrayToStringConversion()
    {
        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'baz' => array(
            ),
        ));
        $this->assertEquals('foo=bar&baz=', $test->curl->response);

        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'baz' => array(
                'qux' => array(
                ),
            ),
        ));
        $this->assertEquals('foo=bar&baz[qux]=', urldecode($test->curl->response));

        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'baz' => array(
                'qux' => array(
                ),
                'wibble' => 'wobble',
            ),
        ));
        $this->assertEquals('foo=bar&baz[qux]=&baz[wibble]=wobble', urldecode($test->curl->response));
    }

    public function testSuccessCallback()
    {
        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->beforeSend(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $before_send_called = true;
        });
        $curl->success(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $success_called = true;
        });
        $curl->error(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            $error_called = true;
        });
        $curl->complete(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertTrue($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        $curl->get(Test::TEST_URL);

        $this->assertTrue($before_send_called);
        $this->assertTrue($success_called);
        $this->assertFalse($error_called);
        $this->assertTrue($complete_called);
    }

    public function testErrorCallback()
    {
        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $curl->beforeSend(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $before_send_called = true;
        });
        $curl->success(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            $success_called = true;
        });
        $curl->error(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $error_called = true;
        });
        $curl->complete(function ($instance) use (
            &$before_send_called, &$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertTrue($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        $curl->get(Test::ERROR_URL);

        $this->assertTrue($before_send_called);
        $this->assertFalse($success_called);
        $this->assertTrue($error_called);
        $this->assertTrue($complete_called);
    }

    public function testClose()
    {
        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'post');
        $curl->post(Test::TEST_URL);
        $this->assertTrue(is_resource($curl->curl));
        $curl->close();
        $this->assertFalse(is_resource($curl->curl));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testRequiredOptionCurlInfoHeaderOutEmitsWarning()
    {
        $curl = new Curl();
        $curl->setOpt(CURLINFO_HEADER_OUT, false);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testRequiredOptionCurlOptReturnTransferEmitsWarning()
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, false);
    }

    public function testRequestMethodSuccessiveGetRequests()
    {
        $test = new Test();
        Helper\test($test, 'GET', 'POST');
        Helper\test($test, 'GET', 'PUT');
        Helper\test($test, 'GET', 'PATCH');
        Helper\test($test, 'GET', 'DELETE');
        Helper\test($test, 'GET', 'HEAD');
        Helper\test($test, 'GET', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePostRequests()
    {
        $test = new Test();
        Helper\test($test, 'POST', 'GET');
        Helper\test($test, 'POST', 'PUT');
        Helper\test($test, 'POST', 'PATCH');
        Helper\test($test, 'POST', 'DELETE');
        Helper\test($test, 'POST', 'HEAD');
        Helper\test($test, 'POST', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePutRequests()
    {
        $test = new Test();
        Helper\test($test, 'PUT', 'GET');
        Helper\test($test, 'PUT', 'POST');
        Helper\test($test, 'PUT', 'PATCH');
        Helper\test($test, 'PUT', 'DELETE');
        Helper\test($test, 'PUT', 'HEAD');
        Helper\test($test, 'PUT', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePatchRequests()
    {
        $test = new Test();
        Helper\test($test, 'PATCH', 'GET');
        Helper\test($test, 'PATCH', 'POST');
        Helper\test($test, 'PATCH', 'PUT');
        Helper\test($test, 'PATCH', 'DELETE');
        Helper\test($test, 'PATCH', 'HEAD');
        Helper\test($test, 'PATCH', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveDeleteRequests()
    {
        $test = new Test();
        Helper\test($test, 'DELETE', 'GET');
        Helper\test($test, 'DELETE', 'POST');
        Helper\test($test, 'DELETE', 'PUT');
        Helper\test($test, 'DELETE', 'PATCH');
        Helper\test($test, 'DELETE', 'HEAD');
        Helper\test($test, 'DELETE', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveHeadRequests()
    {
        $test = new Test();
        Helper\test($test, 'HEAD', 'GET');
        Helper\test($test, 'HEAD', 'POST');
        Helper\test($test, 'HEAD', 'PUT');
        Helper\test($test, 'HEAD', 'PATCH');
        Helper\test($test, 'HEAD', 'DELETE');
        Helper\test($test, 'HEAD', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveOptionsRequests()
    {
        $test = new Test();
        Helper\test($test, 'OPTIONS', 'GET');
        Helper\test($test, 'OPTIONS', 'POST');
        Helper\test($test, 'OPTIONS', 'PUT');
        Helper\test($test, 'OPTIONS', 'PATCH');
        Helper\test($test, 'OPTIONS', 'DELETE');
        Helper\test($test, 'OPTIONS', 'HEAD');
    }
}
