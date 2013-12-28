<?php
// Usage: phpunit --verbose run.php

require '../Curl.class.php';
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
        $test->curl->setopt(CURLOPT_PUT, TRUE);
        $test->curl->setopt(CURLOPT_INFILE, $tmp_file);
        $test->curl->setopt(CURLOPT_INFILESIZE, strlen($png));
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

    public function testError() {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $test->curl->get('http://1.2.3.4/');
        $this->assertTrue($test->curl->error === TRUE);
        $this->assertTrue($test->curl->curl_error === TRUE);
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
    }
}
