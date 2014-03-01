<?php
// Usage: phpunit --verbose run.php

require '../lib/Curl.class.php';
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

    public function testBasicHttpAuth() {
        $test = new Test();
        $this->assertTrue($test->server('http_basic_auth', 'GET') === 'canceled');

        $username = 'myusername';
        $password = 'mypassword';
        $test = new Test();
        $test->curl->setBasicAuthentication($username, $password);
        $test->server('http_basic_auth', 'GET');
        $json = json_decode($test->curl->response);
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

    public function testError() {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $test->curl->get(Test::ERROR_URL);
        $this->assertTrue($test->curl->error === true);
        $this->assertTrue($test->curl->curl_error === true);
        $this->assertTrue($test->curl->curl_error_code === CURLE_OPERATION_TIMEOUTED);
    }

    public function testHeaders() {
        $test = new Test();
        $test->curl->setHeader('Content-Type', 'application/json');
        $test->curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $test->curl->setHeader('Accept', 'application/json');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'CONTENT_TYPE',
        )) === 'application/json');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_X_REQUESTED_WITH',
        )) === 'XMLHttpRequest');
        $this->assertTrue($test->server('server', 'GET', array(
            'key' => 'HTTP_ACCEPT',
        )) === 'application/json');
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
        $this->assertTrue(in_array(
            'Content-Type: application/x-www-form-urlencoded',
            $test->curl->request_headers
        ));

        $test = new Test();
        $test->server('server', 'POST', array(
            'foo' => 'bar',
        ));
        $this->assertTrue(in_array(
            'Expect: 100-continue',
            $test->curl->request_headers
        ));
        $content_type = preg_grep('/^Content-Type: multipart\/form-data; boundary=/',
            $test->curl->request_headers);
        $this->assertTrue(!empty($content_type));
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
        $curl = new Curl();
        $curl->beforeSend(function($instance) {
            $instance->setHeader('X-DEBUG-TEST', 'request_uri');
            $instance->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $instance->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        });
        $curl->get(array(
            Test::TEST_URL . '/a/',
            Test::TEST_URL . '/b/',
            Test::TEST_URL . '/c/',
        ), array(
            'foo' => 'bar',
        ));

        $len = strlen('/a/?foo=bar');
        $this->assertTrue(substr($curl->curls['0']->response, - $len) === '/a/?foo=bar');
        $this->assertTrue(substr($curl->curls['1']->response, - $len) === '/b/?foo=bar');
        $this->assertTrue(substr($curl->curls['2']->response, - $len) === '/c/?foo=bar');
    }

    public function testParallelSetOptions() {
        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'server');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
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

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);

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

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);

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
            Test::TEST_URL . '/a/',
            Test::TEST_URL . '/b/',
            Test::TEST_URL . '/c/',
        ));

        PHPUnit_Framework_Assert::assertTrue($success_called_once || $error_called_once);
        PHPUnit_Framework_Assert::assertTrue($complete_called_once);
    }

    public function testErrorCallback() {
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $curl = new Curl();
        $curl->setHeader('X-DEBUG-TEST', 'get');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
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
        $curl = new Curl();
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

    public function testRequestMethodSuccessiveRequests() {
        $test = new Test();

        function test($instance, $before, $after) {
            $instance->server('server', $before, array('key' => 'REQUEST_METHOD'));
            PHPUnit_Framework_Assert::assertTrue($instance->curl->response === $before);
            $instance->server('server', $after, array('key' => 'REQUEST_METHOD'));
            PHPUnit_Framework_Assert::assertTrue($instance->curl->response === $after);
        }

        test($test, 'GET', 'POST');
        test($test, 'GET', 'PUT');
        test($test, 'GET', 'DELETE');

        test($test, 'POST', 'GET');
        test($test, 'POST', 'PUT');
        test($test, 'POST', 'DELETE');

        test($test, 'PUT', 'GET');
        test($test, 'PUT', 'POST');
        test($test, 'PUT', 'DELETE');

        test($test, 'DELETE', 'GET');
        test($test, 'DELETE', 'POST');
        test($test, 'DELETE', 'PUT');
    }
}
