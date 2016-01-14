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

        $encoded = json_encode($data);

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'data_values');
        $curl->patch(Test::TEST_URL, $data);
        $this->assertEquals($encoded, $curl->rawResponse);
        $this->assertEquals(json_decode($encoded, false), $curl->response);
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
                'APPLICATION/JSON',
                'APPLICATION/JSON; CHARSET=UTF-8',
                'APPLICATION/JSON;CHARSET=UTF-8',
                'application/json',
                'application/json; charset=utf-8',
                'application/json;charset=UTF-8',
                ) as $value) {
                $test = new Test();
                $test->curl->setHeader('Content-Type', $value);
                $this->assertEquals($expected_response, $test->server('post_json', 'POST', json_encode($data)));

                $test = new Test();
                $test->curl->setHeader('Content-Type', $value);
                $this->assertEquals($expected_response, $test->server('post_json', 'POST', $data));
            }
        }
    }

    public function testJSONResponse()
    {
        foreach (array(
            'APPLICATION/JSON',
            'APPLICATION/JSON; CHARSET=UTF-8',
            'APPLICATION/JSON;CHARSET=UTF-8',
            'application/json',
            'application/json; charset=utf-8',
            'application/json;charset=UTF-8',
            ) as $content_type) {
            $test = new Test();
            $test->server('json_response', 'POST', array(
                'key' => 'Content-Type',
                'value' => $content_type,
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
        $test = new Test();
        $not_json_content_types = array();
        $json_content_types = $test->getContentTypes('json',
            $not_json_content_types);

        foreach ($json_content_types as $content_type) {
            $message = "'$content_type' does not normalize ".
                "to a 'json' Content-Type";
            $this->assertEquals('json', $test->curl->normalizeContentType(
                array('Content-Type' => $content_type)), $message);
        }

        foreach ($not_json_content_types as $content_type) {
            $message = "'$content_type' incorrectly normalizes ".
                "to a 'json' Content-Type";
            $this->assertNotEquals('json', $test->curl->normalizeContentType(
                array('Content-Type' => $content_type)), $message);
        }
    }

    public function testXMLContentTypeDetection()
    {
        $test = new Test();
        $not_xml_content_types = array();
        $xml_content_types = $test->getContentTypes('xml',
            $not_xml_content_types);

        foreach ($xml_content_types as $content_type) {
            $message = "'$content_type' does not normalize ".
                "to an 'xml' Content-Type";
            $this->assertEquals('xml', $test->curl->normalizeContentType(
                array('Content-Type' => $content_type)), $message);
        }

        foreach ($not_xml_content_types as $content_type) {
            $message = "'$content_type' incorrectly normalizes ".
                "to an 'xml' Content-Type";
            $this->assertNotEquals('xml', $test->curl->normalizeContentType(
                array('Content-Type' => $content_type)), $message);
        }
    }

    public function testXMLResponse()
    {
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
            'TEXT/XML',
            'TEXT/XML; CHARSET=UTF-8',
            'TEXT/XML;CHARSET=UTF-8',
            ) as $content_type) {
            $test = new Test();
            $test->server('xml_response', 'POST', array(
                'key' => 'Content-Type',
                'value' => $content_type,
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

    public function testMemoryLeak()
    {
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
}
