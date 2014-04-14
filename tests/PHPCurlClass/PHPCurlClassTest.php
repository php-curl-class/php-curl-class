<?php
// Usage: phpunit --verbose run.php

require '../src/Curl.class.php';
require 'helper.inc.php';


class CurlTest extends PHPUnit_Framework_TestCase {
    public function testExtensionLoaded() {
        $this->assertTrue(extension_loaded('curl'));
    }

    public function testArrayAssociative() {
        $this->assertTrue(is_array_assoc(array(
            'foo' => 'wibble',
            'bar' => 'wubble',
            'baz' => 'wobble',
        )));
    }

    public function testArrayIndexed() {
        $this->assertFalse(is_array_assoc(array(
            'wibble',
            'wubble',
            'wobble',
        )));
    }

    public function testCaseInsensitiveArrayGet() {
        $array = new CaseInsensitiveArray();
        $this->assertTrue(is_object($array));
        $this->assertCount(0, $array);
        $this->assertNull($array[(string)rand()]);

        $array['foo'] = 'bar';
        $this->assertNotEmpty($array);
        $this->assertCount(1, $array);
    }

    public function testCaseInsensitiveArraySet() {
        function assertions($array, $count=1) {
            PHPUnit_Framework_Assert::assertCount($count, $array);
            PHPUnit_Framework_Assert::assertTrue($array['foo'] === 'bar');
            PHPUnit_Framework_Assert::assertTrue($array['Foo'] === 'bar');
            PHPUnit_Framework_Assert::assertTrue($array['FOo'] === 'bar');
            PHPUnit_Framework_Assert::assertTrue($array['FOO'] === 'bar');
        }

        $array = new CaseInsensitiveArray();
        $array['foo'] = 'bar';
        assertions($array);

        $array['Foo'] = 'bar';
        assertions($array);

        $array['FOo'] = 'bar';
        assertions($array);

        $array['FOO'] = 'bar';
        assertions($array);

        $array['baz'] = 'qux';
        assertions($array, 2);
    }

    public function testUserAgent() {
        $test = new Test();
        $test->curl->setUserAgent(Curl::USER_AGENT);
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_USER_AGENT',
        )) === Curl::USER_AGENT);
    }

    public function testGet() {
        $test = new Test();
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'REQUEST_METHOD',
        )) === 'GET');
    }

    public function testPostRequestMethod() {
        $test = new Test();
        $this->assertTrue($test->server('server', 'POST', array(
            'key' => 'REQUEST_METHOD',
        )) === 'POST');
    }

    public function testPostData() {
        $test = new Test();
        $this->assertTrue($test->server('post', 'POST', array(
            'key' => 'value',
        )) === 'key=value');
    }

    public function testPostAssociativeArrayData() {
        $test = new Test();
        $this->assertTrue($test->server('post_multidimensional', 'POST', array(
            'username' => 'myusername',
            'password' => 'mypassword',
            'more_data' => array(
                'param1' => 'something',
                'param2' => 'other thing',
                'param3' => 123,
                'param4' => 3.14,
            ),
        )) === 'username=myusername&password=mypassword&more_data%5Bparam1%5D=something&more_data%5Bparam2%5D=other%20thing&more_data%5Bparam3%5D=123&more_data%5Bparam4%5D=3.14');
    }

    public function testPostMultidimensionalData() {
        $test = new Test();
        $this->assertTrue($test->server('post_multidimensional', 'POST', array(
            'key' => 'file',
            'file' => array(
                'wibble',
                'wubble',
                'wobble',
            ),
        )) === 'key=file&file%5B%5D=wibble&file%5B%5D=wubble&file%5B%5D=wobble');
    }

    public function testPostFilePathUpload() {
        $file_path = get_png();

        $test = new Test();
        $this->assertTrue($test->server('post_file_path_upload', 'POST', array(
            'key' => 'image',
            'image' => '@' . $file_path,
        )) === 'image/png');

        unlink($file_path);
        $this->assertFalse(file_exists($file_path));
    }

    public function testPostCurlFileUpload() {
        if (class_exists('CURLFile')) {
            $file_path = get_png();

            $test = new Test();
            $this->assertTrue($test->server('post_file_path_upload', 'POST', array(
                'key' => 'image',
                'image' => new CURLFile($file_path),
            )) === 'image/png');

            unlink($file_path);
            $this->assertFalse(file_exists($file_path));
        }
    }

    public function testPutRequestMethod() {
        $test = new Test();
        $this->assertTrue($test->server('request_method', 'PUT') === 'PUT');
    }

    public function testPutData() {
        $test = new Test();
        $this->assertTrue($test->server('put', 'PUT', array(
            'key' => 'value',
        )) === 'key=value');
    }

    public function testPutFileHandle() {
        $png = create_png();
        $tmp_file = create_tmp_file($png);

        $test = new Test();
        $test->curl->setHeader('X-DEBUG-TEST', 'put_file_handle');
        $test->curl->setOpt(CURLOPT_PUT, true);
        $test->curl->setOpt(CURLOPT_INFILE, $tmp_file);
        $test->curl->setOpt(CURLOPT_INFILESIZE, strlen($png));
        $test->curl->put(Test::TEST_URL);

        fclose($tmp_file);

        $this->assertTrue($test->curl->response === 'image/png');
    }

    public function testPatchRequestMethod() {
        $test = new Test();
        $this->assertTrue($test->server('request_method', 'PATCH') === 'PATCH');
    }

    public function testDelete() {
        $test = new Test();
        $this->assertTrue($test->server('server', 'DELETE', array(
            'key' => 'REQUEST_METHOD',
        )) === 'DELETE');

        $test = new Test();
        $this->assertTrue($test->server('delete', 'DELETE', array(
            'test' => 'delete',
            'key' => 'test',
        )) === 'delete');
    }

    public function testHeadRequestMethod() {
        $test = new Test();
        $test->server('request_method', 'HEAD', array(
            'key' => 'REQUEST_METHOD',
        ));
        $this->assertEquals($test->curl->response_headers['X-REQUEST-METHOD'], 'HEAD');
        $this->assertEmpty($test->curl->response);
    }

    public function testOptionsRequestMethod() {
        $test = new Test();
        $test->server('request_method', 'OPTIONS', array(
            'key' => 'REQUEST_METHOD',
        ));
        $this->assertEquals($test->curl->response_headers['X-REQUEST-METHOD'], 'OPTIONS');
    }

    public function testBasicHttpAuth401Unauthorized() {
        $test = new Test();
        $this->assertTrue($test->server('http_basic_auth', 'GET') === 'canceled');
    }

    public function testBasicHttpAuthSuccess() {
        $username = 'myusername';
        $password = 'mypassword';
        $test = new Test();
        $test->curl->setBasicAuthentication($username, $password);
        $test->server('http_basic_auth', 'GET');
        $json = $test->curl->response;
        $this->assertTrue($json->username === $username);
        $this->assertTrue($json->password === $password);
    }

    public function testReferrer() {
        $test = new Test();
        $test->curl->setReferrer('myreferrer');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_REFERER',
        )) === 'myreferrer');
    }

    public function testCookies() {
        $test = new Test();
        $test->curl->setCookie('mycookie', 'yum');
        $this->assertTrue($test->server('cookie', 'GET', array(
            'key' => 'mycookie',
        )) === 'yum');
    }

    public function testCookieFile() {
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
        $this->assertTrue($test->server('cookie', 'GET', array(
            'key' => 'mycookie',
        )) === 'yum');

        unlink($cookie_file);
        $this->assertFalse(file_exists($cookie_file));
    }

    public function testCookieJar() {
        $cookie_file = dirname(__FILE__) . '/cookies.txt';

        $test = new Test();
        $test->curl->setCookieJar($cookie_file);
        $test->server('cookiejar', 'GET');
        $test->curl->close();

        $this->assertTrue(!(strpos(file_get_contents($cookie_file), "\t" . 'mycookie' . "\t" . 'yum') === false));
        unlink($cookie_file);
        $this->assertFalse(file_exists($cookie_file));
    }

    public function testMultipleCookieResponse() {
        $expected_response = 'cookie1=scrumptious,cookie2=mouthwatering';

        // github.com/facebook/hhvm/issues/2345
        if (defined('HHVM_VERSION')) {
            $expected_response = 'cookie2=mouthwatering,cookie1=scrumptious';
        }

        $test = new Test();
        $test->server('multiple_cookie', 'GET');
        $this->assertEquals($test->curl->response_headers['Set-Cookie'], $expected_response);
    }

    public function testError() {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 4000);
        $test->curl->get(Test::ERROR_URL);
        $this->assertTrue($test->curl->error);
        $this->assertTrue($test->curl->curl_error);
        $this->assertTrue($test->curl->curl_error_code === CURLE_OPERATION_TIMEOUTED);
    }

    public function testErrorMessage() {
        $test = new Test();
        $test->server('error_message', 'GET');

        $expected_response = 'HTTP/1.1 401 Unauthorized';
        if (defined('HHVM_VERSION')) {
            $expected_response = 'HTTP/1.1 401';
        }

        $this->assertEquals($test->curl->error_message, $expected_response);
    }

    public function testHeaders() {
        $test = new Test();
        $test->curl->setHeader('Content-Type', 'application/json');
        $test->curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $test->curl->setHeader('Accept', 'application/json');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_CONTENT_TYPE', // OR "CONTENT_TYPE".
        )) === 'application/json');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_X_REQUESTED_WITH',
        )) === 'XMLHttpRequest');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_ACCEPT',
        )) === 'application/json');
    }

    public function testHeaderCaseSensitivity() {
        $content_type = 'application/json';
        $test = new Test();
        $test->curl->setHeader('Content-Type', $content_type);
        $test->server('response_header', 'GET');

        $request_headers = $test->curl->request_headers;
        $response_headers = $test->curl->response_headers;

        $this->assertEquals($request_headers['Content-Type'], $content_type);
        $this->assertEquals($request_headers['content-type'], $content_type);
        $this->assertEquals($request_headers['CONTENT-TYPE'], $content_type);
        $this->assertEquals($request_headers['cOnTeNt-TyPe'], $content_type);

        $etag = $response_headers['ETag'];
        $this->assertEquals($response_headers['ETAG'], $etag);
        $this->assertEquals($response_headers['etag'], $etag);
        $this->assertEquals($response_headers['eTAG'], $etag);
        $this->assertEquals($response_headers['eTaG'], $etag);
    }

    public function testRequestURL() {
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

    public function testNestedData() {
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
        $this->assertTrue(
            $test->server('post', 'POST', $data) === http_build_query($data)
        );
    }

    public function testPostContentTypes() {
        $test = new Test();
        $test->server('server', 'POST', 'foo=bar');
        $this->assertEquals($test->curl->request_headers['Content-Type'], 'application/x-www-form-urlencoded');

        $test = new Test();
        $test->server('server', 'POST', array(
            'foo' => 'bar',
        ));
        $this->assertEquals($test->curl->request_headers['Expect'], '100-continue');
        preg_match('/^multipart\/form-data; boundary=/', $test->curl->request_headers['Content-Type'], $content_type);
        $this->assertTrue(!empty($content_type));
    }

    public function testJSONResponse() {
        function assertion($key, $value) {
            $test = new Test();
            $test->server('json_response', 'POST', array(
                'key' => $key,
                'value' => $value,
            ));

            $response = $test->curl->response;
            PHPUnit_Framework_Assert::assertNotNull($response);
            PHPUnit_Framework_Assert::assertNull($response->null);
            PHPUnit_Framework_Assert::assertTrue($response->true);
            PHPUnit_Framework_Assert::assertFalse($response->false);
            PHPUnit_Framework_Assert::assertTrue(is_int($response->integer));
            PHPUnit_Framework_Assert::assertTrue(is_float($response->float));
            PHPUnit_Framework_Assert::assertEmpty($response->empty);
            PHPUnit_Framework_Assert::assertTrue(is_string($response->string));
        }

        assertion('Content-Type', 'application/json; charset=utf-8');
        assertion('content-type', 'application/json; charset=utf-8');
        assertion('Content-Type', 'application/json');
        assertion('content-type', 'application/json');
        assertion('CONTENT-TYPE', 'application/json');
        assertion('CONTENT-TYPE', 'APPLICATION/JSON');
    }

    public function testXMLResponse() {
        function xml_assertion($key, $value) {
            $test = new Test();
            $test->server('xml_response', 'POST', array(
                'key' => $key,
                'value' => $value,
            ));

            PHPUnit_Framework_Assert::assertInstanceOf('SimpleXMLElement', $test->curl->response);
        }

        xml_assertion('Content-Type', 'application/rss+xml; charset=utf-8');
        xml_assertion('content-type', 'application/rss+xml; charset=utf-8');
        xml_assertion('Content-Type', 'application/rss+xml');
        xml_assertion('content-type', 'application/rss+xml');
        xml_assertion('CONTENT-TYPE', 'application/rss+xml');
        xml_assertion('CONTENT-TYPE', 'application/rss+xml');
        xml_assertion('Content-Type', 'application/xml; charset=utf-8');
        xml_assertion('content-type', 'application/xml; charset=utf-8');
        xml_assertion('Content-Type', 'application/xml');
        xml_assertion('content-type', 'application/xml');
        xml_assertion('CONTENT-TYPE', 'application/xml');
        xml_assertion('CONTENT-TYPE', 'application/xml');
        xml_assertion('Content-Type', 'text/xml; charset=utf-8');
        xml_assertion('content-type', 'text/xml; charset=utf-8');
        xml_assertion('Content-Type', 'text/xml');
        xml_assertion('content-type', 'text/xml');
        xml_assertion('CONTENT-TYPE', 'text/xml');
        xml_assertion('CONTENT-TYPE', 'text/xml');
    }

    public function testArrayToStringConversion() {
        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'baz' => array(
            ),
        ));
        $this->assertTrue($test->curl->response === 'foo=bar&baz=');

        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'baz' => array(
                'qux' => array(
                ),
            ),
        ));
        $this->assertTrue(urldecode($test->curl->response) ===
            'foo=bar&baz[qux]='
        );

        $test = new Test();
        $test->server('post', 'POST', array(
            'foo' => 'bar',
            'baz' => array(
                'qux' => array(
                ),
                'wibble' => 'wobble',
            ),
        ));
        $this->assertTrue(urldecode($test->curl->response) ===
            'foo=bar&baz[qux]=&baz[wibble]=wobble'
        );
    }

    public function testParallelRequests() {
        $test = new Test();
        $curl = $test->curl;
        $curl->beforeSend(function($instance) {
            $instance->setHeader('X-DEBUG-TEST', 'request_uri');
        });
        $curl->get(array(
            Test::TEST_URL . 'a/',
            Test::TEST_URL . 'b/',
            Test::TEST_URL . 'c/',
        ), array(
            'foo' => 'bar',
        ));

        $len = strlen('/a/?foo=bar');
        $this->assertTrue(substr($curl->curls['0']->response, - $len) === '/a/?foo=bar');
        $this->assertTrue(substr($curl->curls['1']->response, - $len) === '/b/?foo=bar');
        $this->assertTrue(substr($curl->curls['2']->response, - $len) === '/c/?foo=bar');
    }

    public function testParallelSetOptions() {
        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'server');
        $curl->setOpt(CURLOPT_USERAGENT, 'useragent');
        $curl->complete(function($instance) {
            PHPUnit_Framework_Assert::assertTrue($instance->response === 'useragent');
        });
        $curl->get(array(
            Test::TEST_URL,
        ), array(
            'key' => 'HTTP_USER_AGENT',
        ));
    }

    public function testSuccessCallback() {
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'get');

        $curl->success(function($instance) use (&$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $success_called = true;
        });
        $curl->error(function($instance) use (&$success_called, &$error_called, &$complete_called, &$curl) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $error_called = true;
        });
        $curl->complete(function($instance) use (&$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        $curl->get(Test::TEST_URL);

        $this->assertTrue($success_called);
        $this->assertFalse($error_called);
        $this->assertTrue($complete_called);
    }

    public function testParallelSuccessCallback() {
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $success_called_once = false;
        $error_called_once = false;
        $complete_called_once = false;

        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'get');

        $curl->success(function($instance) use (&$success_called,
                                                &$error_called,
                                                &$complete_called,
                                                &$success_called_once) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $success_called = true;
            $success_called_once = true;
        });
        $curl->error(function($instance) use (&$success_called,
                                              &$error_called,
                                              &$complete_called,
                                              &$curl,
                                              &$error_called_once) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $error_called = true;
            $error_called_once = true;
        });
        $curl->complete(function($instance) use (&$success_called,
                                                 &$error_called,
                                                 &$complete_called,
                                                 &$complete_called_once) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
            $complete_called_once = true;

            PHPUnit_Framework_Assert::assertTrue($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertTrue($complete_called);

            $success_called = false;
            $error_called = false;
            $complete_called = false;
        });

        $curl->get(array(
            Test::TEST_URL . 'a/',
            Test::TEST_URL . 'b/',
            Test::TEST_URL . 'c/',
        ));

        PHPUnit_Framework_Assert::assertTrue($success_called_once || $error_called_once);
        PHPUnit_Framework_Assert::assertTrue($complete_called_once);
    }

    public function testErrorCallback() {
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $test = new Test();
        $curl = $test->curl;
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);

        $curl->success(function($instance) use (&$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $success_called = true;
        });
        $curl->error(function($instance) use (&$success_called, &$error_called, &$complete_called, &$curl) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $error_called = true;
        });
        $curl->complete(function($instance) use (&$success_called, &$error_called, &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertTrue($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        $curl->get(Test::ERROR_URL);

        $this->assertFalse($success_called);
        $this->assertTrue($error_called);
        $this->assertTrue($complete_called);
    }

    public function testClose() {
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
    public function testRequiredOptionCurlInfoHeaderOutEmitsWarning() {
        $curl = new Curl();
        $curl->setOpt(CURLINFO_HEADER_OUT, false);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testRequiredOptionCurlOptHeaderEmitsWarning() {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_HEADER, false);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testRequiredOptionCurlOptReturnTransferEmitsWarning() {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, false);
    }

    public function testRequestMethodSuccessiveGetRequests() {
        $test = new Test();
        test($test, 'GET', 'POST');
        test($test, 'GET', 'PUT');
        test($test, 'GET', 'PATCH');
        test($test, 'GET', 'DELETE');
        test($test, 'GET', 'HEAD');
        test($test, 'GET', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePostRequests() {
        $test = new Test();
        test($test, 'POST', 'GET');
        test($test, 'POST', 'PUT');
        test($test, 'POST', 'PATCH');
        test($test, 'POST', 'DELETE');
        test($test, 'POST', 'HEAD');
        test($test, 'POST', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePutRequests() {
        $test = new Test();
        test($test, 'PUT', 'GET');
        test($test, 'PUT', 'POST');
        test($test, 'PUT', 'PATCH');
        test($test, 'PUT', 'DELETE');
        test($test, 'PUT', 'HEAD');
        test($test, 'PUT', 'OPTIONS');
    }

    public function testRequestMethodSuccessivePatchRequests() {
        $test = new Test();
        test($test, 'PATCH', 'GET');
        test($test, 'PATCH', 'POST');
        test($test, 'PATCH', 'PUT');
        test($test, 'PATCH', 'DELETE');
        test($test, 'PATCH', 'HEAD');
        test($test, 'PATCH', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveDeleteRequests() {
        $test = new Test();
        test($test, 'DELETE', 'GET');
        test($test, 'DELETE', 'POST');
        test($test, 'DELETE', 'PUT');
        test($test, 'DELETE', 'PATCH');
        test($test, 'DELETE', 'HEAD');
        test($test, 'DELETE', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveHeadRequests() {
        $test = new Test();
        test($test, 'HEAD', 'GET');
        test($test, 'HEAD', 'POST');
        test($test, 'HEAD', 'PUT');
        test($test, 'HEAD', 'PATCH');
        test($test, 'HEAD', 'DELETE');
        test($test, 'HEAD', 'OPTIONS');
    }

    public function testRequestMethodSuccessiveOptionsRequests() {
        $test = new Test();
        test($test, 'OPTIONS', 'GET');
        test($test, 'OPTIONS', 'POST');
        test($test, 'OPTIONS', 'PUT');
        test($test, 'OPTIONS', 'PATCH');
        test($test, 'OPTIONS', 'DELETE');
        test($test, 'OPTIONS', 'HEAD');
    }
}
