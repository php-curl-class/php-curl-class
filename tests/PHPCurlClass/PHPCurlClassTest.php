<?php

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

    public function testBuildPostDataArgSeparator()
    {
        $data = array(
            'foo' => 'Hello',
            'bar' => 'World',
        );

        foreach (array(false, '&amp;', '&') as $arg_separator) {
            if ($arg_separator) {
                ini_set('arg_separator.output', $arg_separator);
            }
            $curl = new Curl();
            $this->assertEquals('foo=Hello&bar=World', $curl->buildPostData($data));
        }
    }

    public function testUserAgent()
    {
        $php_version = preg_replace('/([\.\+\?\*\(\)\[\]\^\$\/])/', '\\\\\1', 'PHP/' . PHP_VERSION);
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
        $this->assertEquals('GET', $test->server('request_method', 'GET'));
    }

    public function testUrl()
    {
        $data = array('foo' => 'bar');

        // curl -v --get --request GET "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'GET', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);

        // curl -v --request POST "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'POST', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL, $test->curl->url);

        // curl -v --request PUT "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'PUT', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL, $test->curl->url);

        // curl -v --request PATCH "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'PATCH', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL, $test->curl->url);

        // curl -v --request DELETE "http://127.0.0.1:8000/?foo=bar"
        $test = new Test();
        $test->server('server', 'DELETE', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);

        // curl -v --get --request HEAD --head "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'HEAD', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);

        // curl -v --get --request OPTIONS "http://127.0.0.1:8000/" --data "foo=bar"
        $test = new Test();
        $test->server('server', 'OPTIONS', $data);
        $this->assertEquals(Test::TEST_URL, $test->curl->baseUrl);
        $this->assertEquals(Test::TEST_URL . '?' . http_build_query($data), $test->curl->url);
    }

    public function testSetUrlInConstructor()
    {
        $data = array('key' => 'value');

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'delete_with_body');
        $curl->delete($data, array('wibble' => 'wubble'));
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('{"get":{"key":"value"},"delete":{"wibble":"wubble"}}', $curl->rawResponse);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->delete($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->get($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->head($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('HEAD /?key=value HTTP/1.1', $curl->requestHeaders['Request-Line']);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->options($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'request_method');
        $curl->patch($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('PATCH', $curl->response);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'post');
        $curl->post($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl(Test::TEST_URL);
        $curl->setHeader('X-DEBUG-TEST', 'put');
        $curl->put($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);
    }

    public function testSetUrl()
    {
        $data = array('key' => 'value');

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setUrl(Test::TEST_URL);
        $curl->delete($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setUrl(Test::TEST_URL);
        $curl->get($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setUrl(Test::TEST_URL);
        $curl->head($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('HEAD /?key=value HTTP/1.1', $curl->requestHeaders['Request-Line']);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setUrl(Test::TEST_URL);
        $curl->options($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'request_method');
        $curl->setUrl(Test::TEST_URL);
        $curl->patch($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('PATCH', $curl->response);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'post');
        $curl->setUrl(Test::TEST_URL);
        $curl->post($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'put');
        $curl->setUrl(Test::TEST_URL);
        $curl->put($data);
        $this->assertEquals(Test::TEST_URL, $curl->baseUrl);
        $this->assertEquals('key=value', $curl->response);
    }

    public function testEffectiveUrl()
    {
        $test = new Test();
        $test->server('redirect', 'GET');
        $this->assertEquals(Test::TEST_URL, $test->curl->effectiveUrl);

        $test = new Test();
        $test->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $test->server('redirect', 'GET');
        $this->assertEquals(Test::TEST_URL . '?redirect', $test->curl->effectiveUrl);
    }

    public function testPostRequestMethod()
    {
        $test = new Test();
        $this->assertEquals('POST', $test->server('request_method', 'POST'));
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

    public function testPostContentLength()
    {
        $test_data = array(
            array(false, 0),
            array('', 0),
            array(array(), 0),
            array(null, 0),
        );
        foreach ($test_data as $data) {
            $test = new Test();
            list($post_data, $expected_content_length) = $data;
            if ($post_data === false) {
                $test->server('post', 'POST');
            } else {
                $test->server('post', 'POST', $post_data);
            }
            $this->assertEquals($expected_content_length, $test->curl->requestHeaders['Content-Length']);
        }
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

    public function testPostNonFilePathUpload()
    {
        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'file' => '@not-a-file',
        ));
        $this->assertFalse($test->curl->error);
        $this->assertEquals('foo=bar&file=%40not-a-file', $test->curl->response);
    }

    public function testPostRedirectGet()
    {
        // Follow 303 redirection with GET
        $test = new Test();
        $test->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->assertEquals('Redirected: GET', $test->server('post_redirect_get', 'POST'));

        // Follow 303 redirection with POST
        $test = new Test();
        $test->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->assertEquals('Redirected: POST', $test->server('post_redirect_get', 'POST', array(), true));

        // On compatible PHP engines, ensure that it is possible to reuse an existing Curl object
        if ((version_compare(PHP_VERSION, '5.5.11') > 0) && !defined('HHVM_VERSION')) {
            $this->assertEquals('Redirected: GET', $test->server('post_redirect_get', 'POST'));
        }
    }

    public function testPostRedirectGetReuseObjectIncompatibleEngine()
    {
        if ((version_compare(PHP_VERSION, '5.5.11') > 0) && !defined('HHVM_VERSION')) {
            $this->markTestSkipped('This test is not applicable to this platform.');
        }

        try {
            // Follow 303 redirection with POST
            $test = new Test();
            $test->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
            $test->server('post_redirect_get', 'POST', array(), true);

            // On incompatible PHP engines, reusing an existing Curl object to perform a
            // post-redirect-get request will trigger a PHP error
            $test->server('post_redirect_get', 'POST');

            $this->assertTrue(false,
                'Reusing an existing Curl object on incompatible PHP engines shall trigger an error.');
        } catch (PHPUnit_Framework_Error $e) {
            $this->assertTrue(true);
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

    public function testPatchData()
    {
        $test = new Test();
        $this->assertEquals('key=value', $test->server('patch', 'PATCH', array(
            'key' => 'value',
        )));

        $test = new Test();
        $this->assertEquals('{"key":"value"}', $test->server('patch', 'PATCH', json_encode(array(
            'key' => 'value',
        ))));
    }

    public function testPatchRequestMethodWithMultidimArray()
    {
        $data = array(
            'data' => array(
                'foo' => 'bar',
                'wibble' => 'wubble',
            ),
        );

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'data_values');
        $curl->patch(Test::TEST_URL, $data);
        $this->assertEquals('{"data":{"foo":"bar","wibble":"wubble"}}', $curl->rawResponse);
        $this->assertEquals(json_decode(json_encode($data), false), $curl->response);
    }

    public function testDeleteRequestMethod()
    {
        $test = new Test();
        $this->assertEquals('DELETE', $test->server('request_method', 'DELETE'));
    }

    public function testDeleteRequestBody()
    {
        $test = new Test();
        $test->server('delete_with_body', 'DELETE', array('foo' => 'bar'), array('wibble' => 'wubble'));
        $this->assertEquals('{"get":{"foo":"bar"},"delete":{"wibble":"wubble"}}', $test->curl->rawResponse);
    }

    public function testHeadRequestMethod()
    {
        $test = new Test();
        $test->server('request_method', 'HEAD');
        $this->assertEquals('HEAD', $test->curl->responseHeaders['X-REQUEST-METHOD']);
        $this->assertEmpty($test->curl->response);
    }

    public function testOptionsRequestMethod()
    {
        $test = new Test();
        $test->server('request_method', 'OPTIONS');
        $this->assertEquals('OPTIONS', $test->curl->responseHeaders['X-REQUEST-METHOD']);
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
        $this->assertEquals(md5_file($upload_file_path), $upload_test->curl->responseHeaders['ETag']);

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
        $this->assertEquals(md5_file($upload_file_path), $download_test->curl->responseHeaders['ETag']);

        // Ensure successive requests set the appropriate values.
        $this->assertEquals('GET', $download_test->server('request_method', 'GET'));
        $this->assertFalse(is_bool($download_test->curl->response));
        $this->assertFalse(is_bool($download_test->curl->rawResponse));

        // Remove server file.
        $download_test = new Test();
        $this->assertEquals('true', $download_test->server('upload_cleanup', 'POST', array(
            'file_path' => $uploaded_file_path,
        )));

        unlink($upload_file_path);
        unlink($downloaded_file_path);
        $this->assertFalse(file_exists($upload_file_path));
        $this->assertFalse(file_exists($uploaded_file_path));
        $this->assertFalse(file_exists($downloaded_file_path));
    }

    public function testDownloadCallback()
    {
        // Upload a file.
        $upload_file_path = Helper\get_png();
        $upload_test = new Test();
        $upload_test->server('upload_response', 'POST', array(
            'image' => '@' . $upload_file_path,
        ));
        $uploaded_file_path = $upload_test->curl->response->file_path;

        // Download the file.
        $callback_called = false;
        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'download_response');
        $curl->download(Test::TEST_URL . '?' . http_build_query(array(
            'file_path' => $uploaded_file_path,
        )), function($instance, $fh) use (&$callback_called) {
            PHPUnit_Framework_Assert::assertFalse($callback_called);
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue(is_resource($fh));
            PHPUnit_Framework_Assert::assertEquals('stream', get_resource_type($fh));
            PHPUnit_Framework_Assert::assertGreaterThan(0, strlen(stream_get_contents($fh)));
            PHPUnit_Framework_Assert::assertEquals(0, strlen(stream_get_contents($fh)));
            PHPUnit_Framework_Assert::assertTrue(fclose($fh));
            $callback_called = true;
        });
        $this->assertTrue($callback_called);

        // Remove server file.
        $this->assertEquals('true', $upload_test->server('upload_cleanup', 'POST', array(
            'file_path' => $uploaded_file_path,
        )));

        unlink($upload_file_path);
        $this->assertFalse(file_exists($upload_file_path));
        $this->assertFalse(file_exists($uploaded_file_path));
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
        // Skip Digest Access Authentication test on HHVM.
        // https://github.com/facebook/hhvm/issues/5201
        if (defined('HHVM_VERSION')) {
            return;
        }

        $username = 'myusername';
        $password = 'mypassword';
        $invalid_password = 'anotherpassword';

        $test = new Test();
        $test->server('http_digest_auth', 'GET');
        $this->assertEquals('canceled', $test->curl->response);
        $this->assertEquals(401, $test->curl->httpStatusCode);

        $test = new Test();
        $test->curl->setDigestAuthentication($username, $invalid_password);
        $test->server('http_digest_auth', 'GET');
        $this->assertEquals('invalid', $test->curl->response);
        $this->assertEquals(401, $test->curl->httpStatusCode);

        $test = new Test();
        $test->curl->setDigestAuthentication($username, $password);
        $test->server('http_digest_auth', 'GET');
        $this->assertEquals('valid', $test->curl->response);
        $this->assertEquals(200, $test->curl->httpStatusCode);
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

    public function testCookieEncodingSpace()
    {
        $curl = new Curl();
        $curl->setCookie('cookie', 'Om nom nom nom');

        $reflectionClass = new ReflectionClass('\Curl\Curl');
        $reflectionProperty = $reflectionClass->getProperty('options');
        $reflectionProperty->setAccessible(true);
        $options = $reflectionProperty->getValue($curl);
        $this->assertEquals('cookie=Om%20nom%20nom%20nom', $options[CURLOPT_COOKIE]);
    }

    public function testMultipleCookies()
    {
        $curl = new Curl();
        $curl->setCookie('cookie', 'Om nom nom nom');
        $curl->setCookie('foo', 'bar');

        $reflectionClass = new ReflectionClass('\Curl\Curl');
        $reflectionProperty = $reflectionClass->getProperty('options');
        $reflectionProperty->setAccessible(true);
        $options = $reflectionProperty->getValue($curl);
        $this->assertEquals('cookie=Om%20nom%20nom%20nom; foo=bar', $options[CURLOPT_COOKIE]);
    }

    public function testCookieEncodingColon()
    {
        $curl = new Curl();
        $curl->setCookie('JSESSIONID', '0000wd-PcsB3bZ-KzYGAqm_rKlm:17925chrl');

        $reflectionClass = new ReflectionClass('\Curl\Curl');
        $reflectionProperty = $reflectionClass->getProperty('options');
        $reflectionProperty->setAccessible(true);
        $options = $reflectionProperty->getValue($curl);
        $this->assertEquals('JSESSIONID=0000wd-PcsB3bZ-KzYGAqm_rKlm:17925chrl', $options[CURLOPT_COOKIE]);
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
        $this->assertEquals('cookie1=scrumptious,cookie2=mouthwatering', $test->curl->responseHeaders['Set-Cookie']);

        $response_cookies = $test->curl->getResponseCookies();
        $this->assertEquals('scrumptious', $response_cookies['cookie1']);
        $this->assertEquals('mouthwatering', $response_cookies['cookie2']);
    }

    public function testDefaultTimeout()
    {
        $test = new Test();
        $test->server('timeout', 'GET', array(
            'seconds' => '31',
        ));
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curlError);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->errorCode);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curlErrorCode);
        $this->assertFalse($test->curl->httpError);
    }

    public function testTimeoutError()
    {
        $test = new Test();
        $test->curl->setTimeout(5);
        $test->server('timeout', 'GET', array(
            'seconds' => '10',
        ));
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curlError);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->errorCode);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curlErrorCode);
        $this->assertFalse($test->curl->httpError);
    }

    public function testTimeout()
    {
        $test = new Test();
        $test->curl->setTimeout(10);
        $test->server('timeout', 'GET', array(
            'seconds' => '5',
        ));
        $this->assertFalse($test->curl->error);
        $this->assertFalse($test->curl->curlError);
        $this->assertNotEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->errorCode);
        $this->assertNotEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curlErrorCode);
        $this->assertFalse($test->curl->httpError);
    }

    public function testError()
    {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 4000);
        $test->curl->get(Test::ERROR_URL);
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curlError);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->errorCode);
        $this->assertEquals(CURLE_OPERATION_TIMEOUTED, $test->curl->curlErrorCode);
    }

    public function testErrorMessage()
    {
        $test = new Test();
        $test->server('error_message', 'GET');
        $this->assertEquals('HTTP/1.1 401 Unauthorized', $test->curl->errorMessage);
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

        $request_headers = $test->curl->requestHeaders;
        $response_headers = $test->curl->responseHeaders;

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

    public function testHeaderOutOptional()
    {
        // CURLINFO_HEADER_OUT is true by default.
        $test_1 = new Test();
        $test_1->server('response_header', 'GET');
        $this->assertNotEmpty($test_1->curl->requestHeaders);
        $this->assertNotEmpty($test_1->curl->requestHeaders['Request-Line']);

        // CURLINFO_HEADER_OUT is set to true.
        $test_2 = new Test();
        $test_2->curl->setOpt(CURLINFO_HEADER_OUT, true);
        $test_2->server('response_header', 'GET');
        $this->assertNotEmpty($test_2->curl->requestHeaders);
        $this->assertNotEmpty($test_2->curl->requestHeaders['Request-Line']);

        // CURLINFO_HEADER_OUT is set to false.
        $test_3 = new Test();
        $test_3->curl->setOpt(CURLINFO_HEADER_OUT, false);
        $test_3->curl->verbose();
        $test_3->server('response_header', 'GET');
        $this->assertNull($test_3->curl->requestHeaders);
    }

    public function testHeaderRedirect()
    {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $test->server('redirect', 'GET');
        $this->assertEquals('OK', $test->curl->response);
    }

    public function testRequestUrl()
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
        $this->assertEquals('application/x-www-form-urlencoded', $test->curl->requestHeaders['Content-Type']);
    }

    public function testPostArrayUrlEncodedContentType()
    {
        $test = new Test();
        $test->server('server', 'POST', array(
            'foo' => 'bar',
        ));
        $this->assertEquals('application/x-www-form-urlencoded', $test->curl->requestHeaders['Content-Type']);
    }

    public function testPostFileFormDataContentType()
    {
        $file_path = Helper\get_png();

        $test = new Test();
        $test->server('server', 'POST', array(
            'image' => '@' . $file_path,
        ));
        $this->assertEquals('100-continue', $test->curl->requestHeaders['Expect']);
        preg_match('/^multipart\/form-data; boundary=/', $test->curl->requestHeaders['Content-Type'], $content_type);
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
        $this->assertEquals('100-continue', $test->curl->requestHeaders['Expect']);
        preg_match('/^multipart\/form-data; boundary=/', $test->curl->requestHeaders['Content-Type'], $content_type);
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
                )), $test->curl->rawResponse);
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

    public function testJSONContentTypeDetection()
    {
        $json_content_types = array(
            'application/alto-costmap+json',
            'application/alto-costmapfilter+json',
            'application/alto-directory+json',
            'application/alto-endpointprop+json',
            'application/alto-endpointpropparams+json',
            'application/alto-endpointcost+json',
            'application/alto-endpointcostparams+json',
            'application/alto-error+json',
            'application/alto-networkmapfilter+json',
            'application/alto-networkmap+json',
            'application/calendar+json',
            'application/coap-group+json',
            'application/jose+json',
            'application/jrd+json',
            'application/json',
            'application/json-patch+json',
            'application/json-seq',
            'application/jwk+json',
            'application/jwk-set+json',
            'application/ld+json',
            'application/merge-patch+json',
            'application/rdap+json',
            'application/reputon+json',
            'application/vcard+json',
            'application/vnd.apache.thrift.json',
            'application/vnd.api+json',
            'application/vnd.bekitzur-stech+json',
            'application/vnd.collection.doc+json',
            'application/vnd.collection+json',
            'application/vnd.collection.next+json',
            'application/vnd.document+json',
            'application/vnd.geo+json',
            'application/vnd.hal+json',
            'application/vnd.heroku+json',
            'application/vnd.ims.lis.v2.result+json',
            'application/vnd.ims.lti.v2.toolconsumerprofile+json',
            'application/vnd.ims.lti.v2.toolproxy.id+json',
            'application/vnd.ims.lti.v2.toolproxy+json',
            'application/vnd.ims.lti.v2.toolsettings+json',
            'application/vnd.ims.lti.v2.toolsettings.simple+json',
            'application/vnd.mason+json',
            'application/vnd.micro+json',
            'application/vnd.miele+json',
            'application/vnd.oftn.l10n+json',
            'application/vnd.oracle.resource+json',
            'application/vnd.siren+json',
            'application/vnd.xacml+json',

            // Non-standard
            'application/x-json',
            'text/json',
            'text/x-json',
        );

        $class = new ReflectionClass('\Curl\Curl');
        $property = $class->getProperty('jsonPattern');
        $property->setAccessible(true);
        $json_pattern = $property->getValue(new Curl);

        foreach ($json_content_types as $json_content_type) {
            $message = '"' . $json_content_type . '" does not match pattern ' . $json_pattern;
            $this->assertEquals(1, preg_match($json_pattern, $json_content_type), $message);
        }

        $not_json_content_types = array(
            'application/1d-interleaved-parityfec',
            'application/3gpdash-qoe-report+xml',
            'application/3gpp-ims+xml',
            'application/A2L',
            'application/activemessage',
            'application/activemessage',
            'application/AML',
            'application/andrew-inset',
            'application/applefile',
            'application/ATF',
            'application/ATFX',
            'application/atom+xml',
            'application/atomcat+xml',
            'application/atomdeleted+xml',
            'application/atomicmail',
            'application/atomsvc+xml',
            'application/ATXML',
            'application/auth-policy+xml',
            'application/bacnet-xdd+zip',
            'application/batch-SMTP',
            'application/beep+xml',
            'application/calendar+xml',
            'application/call-completion',
            'application/cals-1840',
            'application/cbor',
            'application/ccmp+xml',
            'application/ccxml+xml',
            'application/CDFX+XML',
            'application/cdmi-capability',
            'application/cdmi-container',
            'application/cdmi-domain',
            'application/cdmi-object',
            'application/cdmi-queue',
            'application/CEA',
            'application/cea-2018+xml',
            'application/cellml+xml',
            'application/cfw',
            'application/cms',
            'application/cnrp+xml',
            'application/commonground',
            'application/conference-info+xml',
            'application/cpl+xml',
            'application/csrattrs',
            'application/csta+xml',
            'application/CSTAdata+xml',
            'application/cybercash',
            'application/dash+xml',
            'application/dashdelta',
            'application/davmount+xml',
            'application/dca-rft',
            'application/DCD',
            'application/dec-dx',
            'application/dialog-info+xml',
            'application/dicom',
            'application/DII',
            'application/DIT',
            'application/dns',
            'application/dskpp+xml',
            'application/dssc+der',
            'application/dssc+xml',
            'application/dvcs',
            'application/ecmascript',
            'application/EDI-consent',
            'application/EDIFACT',
            'application/EDI-X12',
            'application/emotionml+xml',
            'application/encaprtp',
            'application/epp+xml',
            'application/epub+zip',
            'application/eshop',
            'application/example',
            'application/fastinfoset',
            'application/fastsoap',
            'application/fdt+xml',
            'application/fits',
            'application/font-sfnt',
            'application/font-tdpfr',
            'application/font-woff',
            'application/framework-attributes+xml',
            'application/gzip',
            'application/H224',
            'application/held+xml',
            'application/http',
            'application/hyperstudio',
            'application/ibe-key-request+xml',
            'application/ibe-pkg-reply+xml',
            'application/ibe-pp-data',
            'application/iges',
            'application/im-iscomposing+xml',
            'application/index',
            'application/index.cmd',
            'application/index.obj',
            'application/index.response',
            'application/index.vnd',
            'application/inkml+xml',
            'application/IOTP',
            'application/ipfix',
            'application/ipp',
            'application/ISUP',
            'application/its+xml',
            'application/javascript',
            'application/jose',
            'application/jwt',
            'application/kpml-request+xml',
            'application/kpml-response+xml',
            'application/link-format',
            'application/load-control+xml',
            'application/lost+xml',
            'application/lostsync+xml',
            'application/LXF',
            'application/mac-binhex40',
            'application/macwriteii',
            'application/mads+xml',
            'application/marc',
            'application/marcxml+xml',
            'application/mathematica',
            'application/mbms-associated-procedure-description+xml',
            'application/mbms-deregister+xml',
            'application/mbms-envelope+xml',
            'application/mbms-msk-response+xml',
            'application/mbms-msk+xml',
            'application/mbms-protection-description+xml',
            'application/mbms-reception-report+xml',
            'application/mbms-register-response+xml',
            'application/mbms-register+xml',
            'application/mbms-schedule+xml',
            'application/mbms-user-service-description+xml',
            'application/mbox+xml',
            'application/media_control+xml',
            'application/media-policy-dataset+xml',
            'application/mediaservercontrol+xml',
            'application/metalink4+xml',
            'application/mets+xml',
            'application/MF4',
            'application/mikey',
            'application/mods+xml',
            'application/moss-keys',
            'application/moss-signature',
            'application/mosskey-data',
            'application/mosskey-request',
            'application/mp21',
            'application/mp4',
            'application/mpeg4-generic',
            'application/mpeg4-iod',
            'application/mpeg4-iod-xmt',
            'application/mrb-consumer+xml',
            'application/mrb-publish+xml',
            'application/msc-ivr+xml',
            'application/msc-mixer+xml',
            'application/msword',
            'application/mxf',
            'application/nasdata',
            'application/news-checkgroups',
            'application/news-groupinfo',
            'application/news-transmission',
            'application/nlsml+xml',
            'application/nss',
            'application/ocsp-request',
            'application/oscp-response',
            'application/octet-stream',
            'application/ODA',
            'application/ODX',
            'application/oebps-package+xml',
            'application/ogg',
            'application/oxps',
            'application/p2p-overlay+xml',
            'application/patch-ops-error+xml',
            'application/pdf',
            'application/PDX',
            'application/pgp-encrypted',
            'application/pgp-signature',
            'application/pidf-diff+xml',
            'application/pidf+xml',
            'application/pkcs10',
            'application/pkcs7-mime',
            'application/pkcs7-signature',
            'application/pkcs8',
            'application/pkix-attr-cert',
            'application/pkix-cert',
            'application/pkix-crl',
            'application/pkix-pkipath',
            'application/pkixcmp',
            'application/pls+xml',
            'application/poc-settings+xml',
            'application/postscript',
            'application/provenance+xml',
            'application/prs.alvestrand.titrax-sheet',
            'application/prs.cww',
            'application/prs.hpub+zip',
            'application/prs.nprend',
            'application/prs.plucker',
            'application/prs.rdf-xml-crypt',
            'application/prs.xsf+xml',
            'application/pskc+xml',
            'application/rdf+xml',
            'application/QSIG',
            'application/raptorfec',
            'application/reginfo+xml',
            'application/relax-ng-compact-syntax',
            'application/remote-printing',
            'application/resource-lists-diff+xml',
            'application/resource-lists+xml',
            'application/riscos',
            'application/rlmi+xml',
            'application/rls-services+xml',
            'application/rpki-ghostbusters',
            'application/rpki-manifest',
            'application/rpki-roa',
            'application/rpki-updown',
            'application/rtf',
            'application/rtploopback',
            'application/rtx',
            'application/samlassertion+xml',
            'application/samlmetadata+xml',
            'application/sbml+xml',
            'application/scaip+xml',
            'application/scvp-cv-request',
            'application/scvp-cv-response',
            'application/scvp-vp-request',
            'application/scvp-vp-response',
            'application/sdp',
            'application/sep-exi',
            'application/sep+xml',
            'application/session-info',
            'application/set-payment',
            'application/set-payment-initiation',
            'application/set-registration',
            'application/set-registration-initiation',
            'application/SGML',
            'application/sgml-open-catalog',
            'application/shf+xml',
            'application/sieve',
            'application/simple-filter+xml',
            'application/simple-message-summary',
            'application/simpleSymbolContainer',
            'application/slate',
            'application/smil',
            'application/smil+xml',
            'application/smpte336m',
            'application/soap+fastinfoset',
            'application/soap+xml',
            'application/spirits-event+xml',
            'application/sql',
            'application/srgs',
            'application/srgs+xml',
            'application/sru+xml',
            'application/ssml+xml',
            'application/tamp-apex-update',
            'application/tamp-apex-update-confirm',
            'application/tamp-community-update',
            'application/tamp-community-update-confirm',
            'application/tamp-error',
            'application/tamp-sequence-adjust',
            'application/tamp-sequence-adjust-confirm',
            'application/tamp-status-query',
            'application/tamp-status-response',
            'application/tamp-update',
            'application/tamp-update-confirm',
            'application/tei+xml',
            'application/thraud+xml',
            'application/timestamp-query',
            'application/timestamp-reply',
            'application/timestamped-data',
            'application/ttml+xml',
            'application/tve-trigger',
            'application/ulpfec',
            'application/urc-grpsheet+xml',
            'application/urc-ressheet+xml',
            'application/urc-targetdesc+xml',
            'application/urc-uisocketdesc+xml',
            'application/vcard+xml',
            'application/vemmi',
            'application/vnd.3gpp.bsf+xml',
            'application/vnd.3gpp.pic-bw-large',
            'application/vnd.3gpp.pic-bw-small',
            'application/vnd.3gpp.pic-bw-var',
            'application/vnd.3gpp.sms',
            'application/vnd.3gpp2.bcmcsinfo+xml',
            'application/vnd.3gpp2.sms',
            'application/vnd.3gpp2.tcap',
            'application/vnd.3M.Post-it-Notes',
            'application/vnd.accpac.simply.aso',
            'application/vnd.accpac.simply.imp',
            'application/vnd-acucobol',
            'application/vnd.acucorp',
            'application/vnd.adobe.flash-movie',
            'application/vnd.adobe.formscentral.fcdt',
            'application/vnd.adobe.fxp',
            'application/vnd.adobe.partial-upload',
            'application/vnd.adobe.xdp+xml',
            'application/vnd.adobe.xfdf',
            'application/vnd.aether.imp',
            'application/vnd.ah-barcode',
            'application/vnd.ahead.space',
            'application/vnd.airzip.filesecure.azf',
            'application/vnd.airzip.filesecure.azs',
            'application/vnd.americandynamics.acc',
            'application/vnd.amiga.ami',
            'application/vnd.amundsen.maze+xml',
            'application/vnd.anser-web-certificate-issue-initiation',
            'application/vnd.antix.game-component',
            'application/vnd.apache.thrift.binary',
            'application/vnd.apache.thrift.compact',
            'application/vnd.apple.mpegurl',
            'application/vnd.apple.installer+xml',
            'application/vnd.arastra.swi',
            'application/vnd.aristanetworks.swi',
            'application/vnd.artsquare',
            'application/vnd.astraea-software.iota',
            'application/vnd.audiograph',
            'application/vnd.autopackage',
            'application/vnd.avistar+xml',
            'application/vnd.balsamiq.bmml+xml',
            'application/vnd.blueice.multipass',
            'application/vnd.bluetooth.ep.oob',
            'application/vnd.bluetooth.le.oob',
            'application/vnd.bmi',
            'application/vnd.businessobjects',
            'application/vnd.cab-jscript',
            'application/vnd.canon-cpdl',
            'application/vnd.canon-lips',
            'application/vnd.cendio.thinlinc.clientconf',
            'application/vnd.century-systems.tcp_stream',
            'application/vnd.chemdraw+xml',
            'application/vnd.chipnuts.karaoke-mmd',
            'application/vnd.cinderella',
            'application/vnd.cirpack.isdn-ext',
            'application/vnd.citationstyles.style+xml',
            'application/vnd.claymore',
            'application/vnd.cloanto.rp9',
            'application/vnd.clonk.c4group',
            'application/vnd.cluetrust.cartomobile-config',
            'application/vnd.cluetrust.cartomobile-config-pkg',
            'application/vnd.coffeescript',
            'application/vnd.commerce-battelle',
            'application/vnd.commonspace',
            'application/vnd.cosmocaller',
            'application/vnd.contact.cmsg',
            'application/vnd.crick.clicker',
            'application/vnd.crick.clicker.keyboard',
            'application/vnd.crick.clicker.palette',
            'application/vnd.crick.clicker.template',
            'application/vnd.crick.clicker.wordbank',
            'application/vnd.criticaltools.wbs+xml',
            'application/vnd.ctc-posml',
            'application/vnd.ctct.ws+xml',
            'application/vnd.cups-pdf',
            'application/vnd.cups-postscript',
            'application/vnd.cups-ppd',
            'application/vnd.cups-raster',
            'application/vnd.cups-raw',
            'application/vnd-curl',
            'application/vnd.cyan.dean.root+xml',
            'application/vnd.cybank',
            'application/vnd-dart',
            'application/vnd.data-vision.rdz',
            'application/vnd.debian.binary-package',
            'application/vnd.dece.data',
            'application/vnd.dece.ttml+xml',
            'application/vnd.dece.unspecified',
            'application/vnd.dece-zip',
            'application/vnd.denovo.fcselayout-link',
            'application/vnd.desmume-movie',
            'application/vnd.dir-bi.plate-dl-nosuffix',
            'application/vnd.dm.delegation+xml',
            'application/vnd.dna',
            'application/vnd.dolby.mobile.1',
            'application/vnd.dolby.mobile.2',
            'application/vnd.doremir.scorecloud-binary-document',
            'application/vnd.dpgraph',
            'application/vnd.dreamfactory',
            'application/vnd.dtg.local',
            'application/vnd.dtg.local.flash',
            'application/vnd.dtg.local.html',
            'application/vnd.dvb.ait',
            'application/vnd.dvb.dvbj',
            'application/vnd.dvb.esgcontainer',
            'application/vnd.dvb.ipdcdftnotifaccess',
            'application/vnd.dvb.ipdcesgaccess',
            'application/vnd.dvb.ipdcesgaccess2',
            'application/vnd.dvb.ipdcesgpdd',
            'application/vnd.dvb.ipdcroaming',
            'application/vnd.dvb.iptv.alfec-base',
            'application/vnd.dvb.iptv.alfec-enhancement',
            'application/vnd.dvb.notif-aggregate-root+xml',
            'application/vnd.dvb.notif-container+xml',
            'application/vnd.dvb.notif-generic+xml',
            'application/vnd.dvb.notif-ia-msglist+xml',
            'application/vnd.dvb.notif-ia-registration-request+xml',
            'application/vnd.dvb.notif-ia-registration-response+xml',
            'application/vnd.dvb.notif-init+xml',
            'application/vnd.dvb.pfr',
            'application/vnd.dvb_service',
            'application/vnd-dxr',
            'application/vnd.dynageo',
            'application/vnd.dzr',
            'application/vnd.easykaraoke.cdgdownload',
            'application/vnd.ecdis-update',
            'application/vnd.ecowin.chart',
            'application/vnd.ecowin.filerequest',
            'application/vnd.ecowin.fileupdate',
            'application/vnd.ecowin.series',
            'application/vnd.ecowin.seriesrequest',
            'application/vnd.ecowin.seriesupdate',
            'application/vnd.emclient.accessrequest+xml',
            'application/vnd.enliven',
            'application/vnd.enphase.envoy',
            'application/vnd.eprints.data+xml',
            'application/vnd.epson.esf',
            'application/vnd.epson.msf',
            'application/vnd.epson.quickanime',
            'application/vnd.epson.salt',
            'application/vnd.epson.ssf',
            'application/vnd.ericsson.quickcall',
            'application/vnd.eszigno3+xml',
            'application/vnd.etsi.aoc+xml',
            'application/vnd.etsi.asic-s+zip',
            'application/vnd.etsi.asic-e+zip',
            'application/vnd.etsi.cug+xml',
            'application/vnd.etsi.iptvcommand+xml',
            'application/vnd.etsi.iptvdiscovery+xml',
            'application/vnd.etsi.iptvprofile+xml',
            'application/vnd.etsi.iptvsad-bc+xml',
            'application/vnd.etsi.iptvsad-cod+xml',
            'application/vnd.etsi.iptvsad-npvr+xml',
            'application/vnd.etsi.iptvservice+xml',
            'application/vnd.etsi.iptvsync+xml',
            'application/vnd.etsi.iptvueprofile+xml',
            'application/vnd.etsi.mcid+xml',
            'application/vnd.etsi.mheg5',
            'application/vnd.etsi.overload-control-policy-dataset+xml',
            'application/vnd.etsi.pstn+xml',
            'application/vnd.etsi.sci+xml',
            'application/vnd.etsi.simservs+xml',
            'application/vnd.etsi.timestamp-token',
            'application/vnd.etsi.tsl+xml',
            'application/vnd.etsi.tsl.der',
            'application/vnd.eudora.data',
            'application/vnd.ezpix-album',
            'application/vnd.ezpix-package',
            'application/vnd.f-secure.mobile',
            'application/vnd.fastcopy-disk-image',
            'application/vnd-fdf',
            'application/vnd.fdsn.mseed',
            'application/vnd.fdsn.seed',
            'application/vnd.ffsns',
            'application/vnd.fints',
            'application/vnd.FloGraphIt',
            'application/vnd.fluxtime.clip',
            'application/vnd.font-fontforge-sfd',
            'application/vnd.framemaker',
            'application/vnd.frogans.fnc',
            'application/vnd.frogans.ltf',
            'application/vnd.fsc.weblaunch',
            'application/vnd.fujitsu.oasys',
            'application/vnd.fujitsu.oasys2',
            'application/vnd.fujitsu.oasys3',
            'application/vnd.fujitsu.oasysgp',
            'application/vnd.fujitsu.oasysprs',
            'application/vnd.fujixerox.ART4',
            'application/vnd.fujixerox.ART-EX',
            'application/vnd.fujixerox.ddd',
            'application/vnd.fujixerox.docuworks',
            'application/vnd.fujixerox.docuworks.binder',
            'application/vnd.fujixerox.docuworks.container',
            'application/vnd.fujixerox.HBPL',
            'application/vnd.fut-misnet',
            'application/vnd.fuzzysheet',
            'application/vnd.genomatix.tuxedo',
            'application/vnd.geocube+xml',
            'application/vnd.geogebra.file',
            'application/vnd.geogebra.tool',
            'application/vnd.geometry-explorer',
            'application/vnd.geonext',
            'application/vnd.geoplan',
            'application/vnd.geospace',
            'application/vnd.gerber',
            'application/vnd.globalplatform.card-content-mgt',
            'application/vnd.globalplatform.card-content-mgt-response',
            'application/vnd.gmx',
            'application/vnd.google-earth.kml+xml',
            'application/vnd.google-earth.kmz',
            'application/vnd.gov.sk.e-form+xml',
            'application/vnd.gov.sk.e-form+zip',
            'application/vnd.gov.sk.xmldatacontainer+xml',
            'application/vnd.grafeq',
            'application/vnd.gridmp',
            'application/vnd.groove-account',
            'application/vnd.groove-help',
            'application/vnd.groove-identity-message',
            'application/vnd.groove-injector',
            'application/vnd.groove-tool-message',
            'application/vnd.groove-tool-template',
            'application/vnd.groove-vcard',
            'application/vnd.hal+xml',
            'application/vnd.HandHeld-Entertainment+xml',
            'application/vnd.hbci',
            'application/vnd.hcl-bireports',
            'application/vnd.hhe.lesson-player',
            'application/vnd.hp-HPGL',
            'application/vnd.hp-hpid',
            'application/vnd.hp-hps',
            'application/vnd.hp-jlyt',
            'application/vnd.hp-PCL',
            'application/vnd.hp-PCLXL',
            'application/vnd.httphone',
            'application/vnd.hydrostatix.sof-data',
            'application/vnd.hzn-3d-crossword',
            'application/vnd.ibm.afplinedata',
            'application/vnd.ibm.electronic-media',
            'application/vnd.ibm.MiniPay',
            'application/vnd.ibm.modcap',
            'application/vnd.ibm.rights-management',
            'application/vnd.ibm.secure-container',
            'application/vnd.iccprofile',
            'application/vnd.ieee.1905',
            'application/vnd.igloader',
            'application/vnd.immervision-ivp',
            'application/vnd.immervision-ivu',
            'application/vnd.ims.imsccv1p1',
            'application/vnd.ims.imsccv1p2',
            'application/vnd.ims.imsccv1p3',
            'application/vnd.informedcontrol.rms+xml',
            'application/vnd.infotech.project',
            'application/vnd.infotech.project+xml',
            'application/vnd.informix-visionary',
            'application/vnd.innopath.wamp.notification',
            'application/vnd.insors.igm',
            'application/vnd.intercon.formnet',
            'application/vnd.intergeo',
            'application/vnd.intertrust.digibox',
            'application/vnd.intertrust.nncp',
            'application/vnd.intu.qbo',
            'application/vnd.intu.qfx',
            'application/vnd.iptc.g2.catalogitem+xml',
            'application/vnd.iptc.g2.conceptitem+xml',
            'application/vnd.iptc.g2.knowledgeitem+xml',
            'application/vnd.iptc.g2.newsitem+xml',
            'application/vnd.iptc.g2.newsmessage+xml',
            'application/vnd.iptc.g2.packageitem+xml',
            'application/vnd.iptc.g2.planningitem+xml',
            'application/vnd.ipunplugged.rcprofile',
            'application/vnd.irepository.package+xml',
            'application/vnd.is-xpr',
            'application/vnd.isac.fcs',
            'application/vnd.jam',
            'application/vnd.japannet-directory-service',
            'application/vnd.japannet-jpnstore-wakeup',
            'application/vnd.japannet-payment-wakeup',
            'application/vnd.japannet-registration',
            'application/vnd.japannet-registration-wakeup',
            'application/vnd.japannet-setstore-wakeup',
            'application/vnd.japannet-verification',
            'application/vnd.japannet-verification-wakeup',
            'application/vnd.jcp.javame.midlet-rms',
            'application/vnd.jisp',
            'application/vnd.joost.joda-archive',
            'application/vnd.jsk.isdn-ngn',
            'application/vnd.kahootz',
            'application/vnd.kde.karbon',
            'application/vnd.kde.kchart',
            'application/vnd.kde.kformula',
            'application/vnd.kde.kivio',
            'application/vnd.kde.kontour',
            'application/vnd.kde.kpresenter',
            'application/vnd.kde.kspread',
            'application/vnd.kde.kword',
            'application/vnd.kenameaapp',
            'application/vnd.kidspiration',
            'application/vnd.Kinar',
            'application/vnd.koan',
            'application/vnd.kodak-descriptor',
            'application/vnd.las.las+xml',
            'application/vnd.liberty-request+xml',
            'application/vnd.llamagraphics.life-balance.desktop',
            'application/vnd.llamagraphics.life-balance.exchange+xml',
            'application/vnd.lotus-1-2-3',
            'application/vnd.lotus-approach',
            'application/vnd.lotus-freelance',
            'application/vnd.lotus-notes',
            'application/vnd.lotus-organizer',
            'application/vnd.lotus-screencam',
            'application/vnd.lotus-wordpro',
            'application/vnd.macports.portpkg',
            'application/vnd.marlin.drm.actiontoken+xml',
            'application/vnd.marlin.drm.conftoken+xml',
            'application/vnd.marlin.drm.license+xml',
            'application/vnd.marlin.drm.mdcf',
            'application/vnd.maxmind.maxmind-db',
            'application/vnd.mcd',
            'application/vnd.medcalcdata',
            'application/vnd.mediastation.cdkey',
            'application/vnd.meridian-slingshot',
            'application/vnd.MFER',
            'application/vnd.mfmp',
            'application/vnd.micrografx.flo',
            'application/vnd.micrografx-igx',
            'application/vnd.microsoft.portable-executable',
            'application/vnd-mif',
            'application/vnd.minisoft-hp3000-save',
            'application/vnd.mitsubishi.misty-guard.trustweb',
            'application/vnd.Mobius.DAF',
            'application/vnd.Mobius.DIS',
            'application/vnd.Mobius.MBK',
            'application/vnd.Mobius.MQY',
            'application/vnd.Mobius.MSL',
            'application/vnd.Mobius.PLC',
            'application/vnd.Mobius.TXF',
            'application/vnd.mophun.application',
            'application/vnd.mophun.certificate',
            'application/vnd.motorola.flexsuite',
            'application/vnd.motorola.flexsuite.adsi',
            'application/vnd.motorola.flexsuite.fis',
            'application/vnd.motorola.flexsuite.gotap',
            'application/vnd.motorola.flexsuite.kmr',
            'application/vnd.motorola.flexsuite.ttc',
            'application/vnd.motorola.flexsuite.wem',
            'application/vnd.motorola.iprm',
            'application/vnd.mozilla.xul+xml',
            'application/vnd.ms-artgalry',
            'application/vnd.ms-asf',
            'application/vnd.ms-cab-compressed',
            'application/vnd.ms-3mfdocument',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-fontobject',
            'application/vnd.ms-htmlhelp',
            'application/vnd.ms-ims',
            'application/vnd.ms-lrm',
            'application/vnd.ms-office.activeX+xml',
            'application/vnd.ms-officetheme',
            'application/vnd.ms-playready.initiator+xml',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-project',
            'application/vnd.ms-tnef',
            'application/vnd.ms-windows.printerpairing',
            'application/vnd.ms-wmdrm.lic-chlg-req',
            'application/vnd.ms-wmdrm.lic-resp',
            'application/vnd.ms-wmdrm.meter-chlg-req',
            'application/vnd.ms-wmdrm.meter-resp',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-word.template.macroEnabled.12',
            'application/vnd.ms-works',
            'application/vnd.ms-wpl',
            'application/vnd.ms-xpsdocument',
            'application/vnd.msa-disk-image',
            'application/vnd.mseq',
            'application/vnd.msign',
            'application/vnd.multiad.creator',
            'application/vnd.multiad.creator.cif',
            'application/vnd.musician',
            'application/vnd.music-niff',
            'application/vnd.muvee.style',
            'application/vnd.mynfc',
            'application/vnd.ncd.control',
            'application/vnd.ncd.reference',
            'application/vnd.nervana',
            'application/vnd.netfpx',
            'application/vnd.neurolanguage.nlu',
            'application/vnd.nintendo.snes.rom',
            'application/vnd.nintendo.nitro.rom',
            'application/vnd.nitf',
            'application/vnd.noblenet-directory',
            'application/vnd.noblenet-sealer',
            'application/vnd.noblenet-web',
            'application/vnd.nokia.catalogs',
            'application/vnd.nokia.conml+wbxml',
            'application/vnd.nokia.conml+xml',
            'application/vnd.nokia.iptv.config+xml',
            'application/vnd.nokia.iSDS-radio-presets',
            'application/vnd.nokia.landmark+wbxml',
            'application/vnd.nokia.landmark+xml',
            'application/vnd.nokia.landmarkcollection+xml',
            'application/vnd.nokia.ncd',
            'application/vnd.nokia.n-gage.ac+xml',
            'application/vnd.nokia.n-gage.data',
            'application/vnd.nokia.n-gage.symbian.install',
            'application/vnd.nokia.pcd+wbxml',
            'application/vnd.nokia.pcd+xml',
            'application/vnd.nokia.radio-preset',
            'application/vnd.nokia.radio-presets',
            'application/vnd.novadigm.EDM',
            'application/vnd.novadigm.EDX',
            'application/vnd.novadigm.EXT',
            'application/vnd.ntt-local.content-share',
            'application/vnd.ntt-local.file-transfer',
            'application/vnd.ntt-local.ogw_remote-access',
            'application/vnd.ntt-local.sip-ta_remote',
            'application/vnd.ntt-local.sip-ta_tcp_stream',
            'application/vnd.oasis.opendocument.chart',
            'application/vnd.oasis.opendocument.chart-template',
            'application/vnd.oasis.opendocument.database',
            'application/vnd.oasis.opendocument.formula',
            'application/vnd.oasis.opendocument.formula-template',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.graphics-template',
            'application/vnd.oasis.opendocument.image',
            'application/vnd.oasis.opendocument.image-template',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.presentation-template',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.spreadsheet-template',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.text-master',
            'application/vnd.oasis.opendocument.text-template',
            'application/vnd.oasis.opendocument.text-web',
            'application/vnd.obn',
            'application/vnd.oipf.contentaccessdownload+xml',
            'application/vnd.oipf.contentaccessstreaming+xml',
            'application/vnd.oipf.cspg-hexbinary',
            'application/vnd.oipf.dae.svg+xml',
            'application/vnd.oipf.dae.xhtml+xml',
            'application/vnd.oipf.mippvcontrolmessage+xml',
            'application/vnd.oipf.pae.gem',
            'application/vnd.oipf.spdiscovery+xml',
            'application/vnd.oipf.spdlist+xml',
            'application/vnd.oipf.ueprofile+xml',
            'application/vnd.oipf.userprofile+xml',
            'application/vnd.olpc-sugar',
            'application/vnd.oma.bcast.associated-procedure-parameter+xml',
            'application/vnd.oma.bcast.drm-trigger+xml',
            'application/vnd.oma.bcast.imd+xml',
            'application/vnd.oma.bcast.ltkm',
            'application/vnd.oma.bcast.notification+xml',
            'application/vnd.oma.bcast.provisioningtrigger',
            'application/vnd.oma.bcast.sgboot',
            'application/vnd.oma.bcast.sgdd+xml',
            'application/vnd.oma.bcast.sgdu',
            'application/vnd.oma.bcast.simple-symbol-container',
            'application/vnd.oma.bcast.smartcard-trigger+xml',
            'application/vnd.oma.bcast.sprov+xml',
            'application/vnd.oma.bcast.stkm',
            'application/vnd.oma.cab-address-book+xml',
            'application/vnd.oma.cab-feature-handler+xml',
            'application/vnd.oma.cab-pcc+xml',
            'application/vnd.oma.cab-subs-invite+xml',
            'application/vnd.oma.cab-user-prefs+xml',
            'application/vnd.oma.dcd',
            'application/vnd.oma.dcdc',
            'application/vnd.oma.dd2+xml',
            'application/vnd.oma.drm.risd+xml',
            'application/vnd.oma.group-usage-list+xml',
            'application/vnd.oma.pal+xml',
            'application/vnd.oma.poc.detailed-progress-report+xml',
            'application/vnd.oma.poc.final-report+xml',
            'application/vnd.oma.poc.groups+xml',
            'application/vnd.oma.poc.invocation-descriptor+xml',
            'application/vnd.oma.poc.optimized-progress-report+xml',
            'application/vnd.oma.push',
            'application/vnd.oma.scidm.messages+xml',
            'application/vnd.oma.xcap-directory+xml',
            'application/vnd.omads-email+xml',
            'application/vnd.omads-file+xml',
            'application/vnd.omads-folder+xml',
            'application/vnd.omaloc-supl-init',
            'application/vnd.oma-scws-config',
            'application/vnd.oma-scws-http-request',
            'application/vnd.oma-scws-http-response',
            'application/vnd.openeye.oeb',
            'application/vnd.openxmlformats-officedocument.custom-properties+xml',
            'application/vnd.openxmlformats-officedocument.customXmlProperties+xml',
            'application/vnd.openxmlformats-officedocument.drawing+xml',
            'application/vnd.openxmlformats-officedocument.drawingml.chart+xml',
            'application/vnd.openxmlformats-officedocument.drawingml.chartshapes+xml',
            'application/vnd.openxmlformats-officedocument.drawingml.diagramColors+xml',
            'application/vnd.openxmlformats-officedocument.drawingml.diagramData+xml',
            'application/vnd.openxmlformats-officedocument.drawingml.diagramLayout+xml',
            'application/vnd.openxmlformats-officedocument.drawingml.diagramStyle+xml',
            'application/vnd.openxmlformats-officedocument.extended-properties+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.commentAuthors+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.comments+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.handoutMaster+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.notesMaster+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.notesSlide+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.presProps+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'application/vnd.openxmlformats-officedocument.presentationml.slide+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow.main+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.slideUpdateInfo+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.tableStyles+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.tags+xml',
            'application/vnd.openxmlformats-officedocument.presentationml-template',
            'application/vnd.openxmlformats-officedocument.presentationml.template.main+xml',
            'application/vnd.openxmlformats-officedocument.presentationml.viewProps+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.calcChain+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.chartsheet+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.comments+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.connections+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.dialogsheet+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.externalLink+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.pivotCacheDefinition+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.pivotCacheRecords+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.pivotTable+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.queryTable+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.revisionHeaders+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.revisionLog+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheetMetadata+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.table+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.tableSingleCells+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml-template',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template.main+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.userNames+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.volatileDependencies+xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml',
            'application/vnd.openxmlformats-officedocument.theme+xml',
            'application/vnd.openxmlformats-officedocument.themeOverride+xml',
            'application/vnd.openxmlformats-officedocument.vmlDrawing',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.comments+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document.glossary+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.endnotes+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.fontTable+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.footnotes+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml-template',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template.main+xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.webSettings+xml',
            'application/vnd.openxmlformats-package.core-properties+xml',
            'application/vnd.openxmlformats-package.digital-signature-xmlsignature+xml',
            'application/vnd.openxmlformats-package.relationships+xml',
            'application/vnd.orange.indata',
            'application/vnd.osa.netdeploy',
            'application/vnd.osgeo.mapguide.package',
            'application/vnd.osgi.bundle',
            'application/vnd.osgi.dp',
            'application/vnd.osgi.subsystem',
            'application/vnd.otps.ct-kip+xml',
            'application/vnd.palm',
            'application/vnd.panoply',
            'application/vnd.paos+xml',
            'application/vnd.pawaafile',
            'application/vnd.pcos',
            'application/vnd.pg.format',
            'application/vnd.pg.osasli',
            'application/vnd.piaccess.application-licence',
            'application/vnd.picsel',
            'application/vnd.pmi.widget',
            'application/vnd.poc.group-advertisement+xml',
            'application/vnd.pocketlearn',
            'application/vnd.powerbuilder6',
            'application/vnd.powerbuilder6-s',
            'application/vnd.powerbuilder7',
            'application/vnd.powerbuilder75',
            'application/vnd.powerbuilder75-s',
            'application/vnd.powerbuilder7-s',
            'application/vnd.preminet',
            'application/vnd.previewsystems.box',
            'application/vnd.proteus.magazine',
            'application/vnd.publishare-delta-tree',
            'application/vnd.pvi.ptid1',
            'application/vwg-multiplexed',
            'application/vnd.pwg-xhtml-print+xml',
            'application/vnd.qualcomm.brew-app-res',
            'application/vnd.Quark.QuarkXPress',
            'application/vnd.quobject-quoxdocument',
            'application/vnd.radisys.moml+xml',
            'application/vnd.radisys.msml-audit-conf+xml',
            'application/vnd.radisys.msml-audit-conn+xml',
            'application/vnd.radisys.msml-audit-dialog+xml',
            'application/vnd.radisys.msml-audit-stream+xml',
            'application/vnd.radisys.msml-audit+xml',
            'application/vnd.radisys.msml-conf+xml',
            'application/vnd.radisys.msml-dialog-base+xml',
            'application/vnd.radisys.msml-dialog-fax-detect+xml',
            'application/vnd.radisys.msml-dialog-fax-sendrecv+xml',
            'application/vnd.radisys.msml-dialog-group+xml',
            'application/vnd.radisys.msml-dialog-speech+xml',
            'application/vnd.radisys.msml-dialog-transform+xml',
            'application/vnd.radisys.msml-dialog+xml',
            'application/vnd.radisys.msml+xml',
            'application/vnd.rainstor.data',
            'application/vnd.rapid',
            'application/vnd.realvnc.bed',
            'application/vnd.recordare.musicxml',
            'application/vnd.recordare.musicxml+xml',
            'application/vnd.renlearn.rlprint',
            'application/vnd.rig.cryptonote',
            'application/vnd.route66.link66+xml',
            'application/vnd.rs-274x',
            'application/vnd.ruckus.download',
            'application/vnd.s3sms',
            'application/vnd.sailingtracker.track',
            'application/vnd.sbm.cid',
            'application/vnd.sbm.mid2',
            'application/vnd.scribus',
            'application/vnd.sealed.3df',
            'application/vnd.sealed.csf',
            'application/vnd.sealed-doc',
            'application/vnd.sealed-eml',
            'application/vnd.sealed-mht',
            'application/vnd.sealed.net',
            'application/vnd.sealed-ppt',
            'application/vnd.sealed-tiff',
            'application/vnd.sealed-xls',
            'application/vnd.sealedmedia.softseal-html',
            'application/vnd.sealedmedia.softseal-pdf',
            'application/vnd.seemail',
            'application/vnd-sema',
            'application/vnd.semd',
            'application/vnd.semf',
            'application/vnd.shana.informed.formdata',
            'application/vnd.shana.informed.formtemplate',
            'application/vnd.shana.informed.interchange',
            'application/vnd.shana.informed.package',
            'application/vnd.SimTech-MindMapper',
            'application/vnd.smaf',
            'application/vnd.smart.notebook',
            'application/vnd.smart.teacher',
            'application/vnd.software602.filler.form+xml',
            'application/vnd.software602.filler.form-xml-zip',
            'application/vnd.solent.sdkm+xml',
            'application/vnd.spotfire.dxp',
            'application/vnd.spotfire.sfs',
            'application/vnd.sss-cod',
            'application/vnd.sss-dtf',
            'application/vnd.sss-ntf',
            'application/vnd.stepmania.package',
            'application/vnd.stepmania.stepchart',
            'application/vnd.street-stream',
            'application/vnd.sun.wadl+xml',
            'application/vnd.sus-calendar',
            'application/vnd.svd',
            'application/vnd.swiftview-ics',
            'application/vnd.syncml.dm.notification',
            'application/vnd.syncml.dmddf+xml',
            'application/vnd.syncml.dmtnds+wbxml',
            'application/vnd.syncml.dmtnds+xml',
            'application/vnd.syncml.dmddf+wbxml',
            'application/vnd.syncml.dm+wbxml',
            'application/vnd.syncml.dm+xml',
            'application/vnd.syncml.ds.notification',
            'application/vnd.syncml+xml',
            'application/vnd.tao.intent-module-archive',
            'application/vnd.tcpdump.pcap',
            'application/vnd.tmd.mediaflex.api+xml',
            'application/vnd.tmobile-livetv',
            'application/vnd.trid.tpt',
            'application/vnd.triscape.mxs',
            'application/vnd.trueapp',
            'application/vnd.truedoc',
            'application/vnd.ubisoft.webplayer',
            'application/vnd.ufdl',
            'application/vnd.uiq.theme',
            'application/vnd.umajin',
            'application/vnd.unity',
            'application/vnd.uoml+xml',
            'application/vnd.uplanet.alert',
            'application/vnd.uplanet.alert-wbxml',
            'application/vnd.uplanet.bearer-choice',
            'application/vnd.uplanet.bearer-choice-wbxml',
            'application/vnd.uplanet.cacheop',
            'application/vnd.uplanet.cacheop-wbxml',
            'application/vnd.uplanet.channel',
            'application/vnd.uplanet.channel-wbxml',
            'application/vnd.uplanet.list',
            'application/vnd.uplanet.listcmd',
            'application/vnd.uplanet.listcmd-wbxml',
            'application/vnd.uplanet.list-wbxml',
            'application/vnd.uplanet.signal',
            'application/vnd.valve.source.material',
            'application/vnd.vcx',
            'application/vnd.vd-study',
            'application/vnd.vectorworks',
            'application/vnd.verimatrix.vcas',
            'application/vnd.vidsoft.vidconference',
            'application/vnd.visio',
            'application/vnd.visionary',
            'application/vnd.vividence.scriptfile',
            'application/vnd.vsf',
            'application/vnd.wap.sic',
            'application/vnd.wap-slc',
            'application/vnd.wap-wbxml',
            'application/vnd-wap-wmlc',
            'application/vnd.wap.wmlscriptc',
            'application/vnd.webturbo',
            'application/vnd.wfa.p2p',
            'application/vnd.wfa.wsc',
            'application/vnd.windows.devicepairing',
            'application/vnd.wmc',
            'application/vnd.wmf.bootstrap',
            'application/vnd.wolfram.mathematica',
            'application/vnd.wolfram.mathematica.package',
            'application/vnd.wolfram.player',
            'application/vnd.wordperfect',
            'application/vnd.wqd',
            'application/vnd.wrq-hp3000-labelled',
            'application/vnd.wt.stf',
            'application/vnd.wv.csp+xml',
            'application/vnd.wv.csp+wbxml',
            'application/vnd.wv.ssp+xml',
            'application/vnd.xara',
            'application/vnd.xfdl',
            'application/vnd.xfdl.webform',
            'application/vnd.xmi+xml',
            'application/vnd.xmpie.cpkg',
            'application/vnd.xmpie.dpkg',
            'application/vnd.xmpie.plan',
            'application/vnd.xmpie.ppkg',
            'application/vnd.xmpie.xlim',
            'application/vnd.yamaha.hv-dic',
            'application/vnd.yamaha.hv-script',
            'application/vnd.yamaha.hv-voice',
            'application/vnd.yamaha.openscoreformat.osfpvg+xml',
            'application/vnd.yamaha.openscoreformat',
            'application/vnd.yamaha.remote-setup',
            'application/vnd.yamaha.smaf-audio',
            'application/vnd.yamaha.smaf-phrase',
            'application/vnd.yamaha.through-ngn',
            'application/vnd.yamaha.tunnel-udpencap',
            'application/vnd.yaoweme',
            'application/vnd.yellowriver-custom-menu',
            'application/vnd.zul',
            'application/vnd.zzazz.deck+xml',
            'application/voicexml+xml',
            'application/vq-rtcpxr',
            'application/watcherinfo+xml',
            'application/whoispp-query',
            'application/whoispp-response',
            'application/wita',
            'application/wordperfect5.1',
            'application/wsdl+xml',
            'application/wspolicy+xml',
            'application/x-www-form-urlencoded',
            'application/x400-bp',
            'application/xacml+xml',
            'application/xcap-att+xml',
            'application/xcap-caps+xml',
            'application/xcap-diff+xml',
            'application/xcap-el+xml',
            'application/xcap-error+xml',
            'application/xcap-ns+xml',
            'application/xcon-conference-info-diff+xml',
            'application/xcon-conference-info+xml',
            'application/xenc+xml',
            'application/xhtml-voice+xml',
            'application/xhtml+xml',
            'application/xml',
            'application/xml-dtd',
            'application/xml-external-parsed-entity',
            'application/xml-patch+xml',
            'application/xmpp+xml',
            'application/xop+xml',
            'application/xv+xml',
            'application/yang',
            'application/yin+xml',
            'application/zip',
            'application/zlib',
        );

        foreach ($not_json_content_types as $json_content_type) {
            $message = '"' . $json_content_type . '" matches pattern ' . $json_pattern;
            $this->assertEquals(0, preg_match($json_pattern, $json_content_type), $message);
        }
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
                $this->assertEquals($xml, $test->curl->rawResponse);
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
    public function testRequiredOptionCurlOptReturnTransferEmitsWarning()
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, false);
    }

    public function testRequestMethodSuccessiveGetRequests()
    {
        $test = new Test();
        $test->chain_requests('GET', 'POST');
        $test->chain_requests('GET', 'PUT');
        $test->chain_requests('GET', 'PATCH');
        $test->chain_requests('GET', 'DELETE');
        $test->chain_requests('GET', 'HEAD');
        $test->chain_requests('GET', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePostRequests()
    {
        $test = new Test();
        $test->chain_requests('POST', 'GET');
        $test->chain_requests('POST', 'PUT');
        $test->chain_requests('POST', 'PATCH');
        $test->chain_requests('POST', 'DELETE');
        $test->chain_requests('POST', 'HEAD');
        $test->chain_requests('POST', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePutRequests()
    {
        $test = new Test();
        $test->chain_requests('PUT', 'GET');
        $test->chain_requests('PUT', 'POST');
        $test->chain_requests('PUT', 'PATCH');
        $test->chain_requests('PUT', 'DELETE');
        $test->chain_requests('PUT', 'HEAD');
        $test->chain_requests('PUT', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePatchRequests()
    {
        $test = new Test();
        $test->chain_requests('PATCH', 'GET');
        $test->chain_requests('PATCH', 'POST');
        $test->chain_requests('PATCH', 'PUT');
        $test->chain_requests('PATCH', 'DELETE');
        $test->chain_requests('PATCH', 'HEAD');
        $test->chain_requests('PATCH', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveDeleteRequests()
    {
        $test = new Test();
        $test->chain_requests('DELETE', 'GET');
        $test->chain_requests('DELETE', 'POST');
        $test->chain_requests('DELETE', 'PUT');
        $test->chain_requests('DELETE', 'PATCH');
        $test->chain_requests('DELETE', 'HEAD');
        $test->chain_requests('DELETE', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveHeadRequests()
    {
        $test = new Test();
        $test->chain_requests('HEAD', 'GET');
        $test->chain_requests('HEAD', 'POST');
        $test->chain_requests('HEAD', 'PUT');
        $test->chain_requests('HEAD', 'PATCH');
        $test->chain_requests('HEAD', 'DELETE');
        $test->chain_requests('HEAD', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveOptionsRequests()
    {
        $test = new Test();
        $test->chain_requests('OPTIONS', 'GET');
        $test->chain_requests('OPTIONS', 'POST');
        $test->chain_requests('OPTIONS', 'PUT');
        $test->chain_requests('OPTIONS', 'PATCH');
        $test->chain_requests('OPTIONS', 'DELETE');
        $test->chain_requests('OPTIONS', 'HEAD');
    }

    public function testMemoryLeak()
    {
        // Skip memory leak test failing for PHP 7.
        // "Failed asserting that 8192 is less than 1000."
        if (getenv('TRAVIS_PHP_VERSION') === '7.0') {
            return;
        }

        ob_start();
        echo '[';
        for ($i = 0; $i < 10; $i++) {
            if ($i >= 1) {
                echo ',';
            }
            echo '{"before":' . memory_get_usage() . ',';
            $curl = new Curl();
            $curl->close();
            echo '"after":' . memory_get_usage() . '}';
            sleep(1);
        }
        echo ']';
        $html = ob_get_contents();
        ob_end_clean();
        $results = json_decode($html, true);

        // Ensure memory does not leak excessively after instantiating a new
        // Curl instance and cleaning up. Memory diffs in the 2000-6000+ range
        // indicate a memory leak.
        $max_memory_diff = 1000;
        foreach ($results as $i => $result) {
            $memory_diff = $result['after'] - $result['before'];;

            // Skip the first test to allow memory usage to settle.
            if ($i >= 1) {
                $this->assertLessThan($max_memory_diff, $memory_diff);
            }
        }
    }

    public function testAlternativeStandardErrorOutput()
    {
        // Skip test on HHVM due to "Segmentation fault".
        if (defined('HHVM_VERSION')) {
            return;
        }

        $buffer = fopen('php://memory', 'w+');

        $curl = new Curl();
        $curl->verbose(true, $buffer);
        $curl->post(Test::TEST_URL);

        rewind($buffer);
        $stderr = stream_get_contents($buffer);
        fclose($buffer);

        $this->assertNotEmpty($stderr);
    }

    public function testXMLDecoder()
    {
        $data = array(
            'key' => 'Content-Type',
            'value' => 'text/xml',
        );

        $test = new Test();
        $test->server('xml_with_cdata_response', 'POST', $data);
        $this->assertFalse(strpos($test->curl->response->saveXML(), '<![CDATA[') === false);

        $test = new Test();
        $test->curl->setXmlDecoder(function($response) {
            return simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        });
        $test->server('xml_with_cdata_response', 'POST', $data);
        $this->assertTrue(strpos($test->curl->response->saveXML(), '<![CDATA[') === false);
    }
}
