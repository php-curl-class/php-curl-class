<?php
// Usage: phpunit --verbose run.php

require '../Curl.class.php';


class Test {
    const TEST_URL = 'https://127.0.0.1/php-curl-class/tests/server.php';

    function __construct() {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
    }

    function server($request_method, $data='') {
        $request_method = strtolower($request_method);
        $this->curl->$request_method(self::TEST_URL, $data);
        return $this->curl->response;
    }
}

class CurlTest extends PHPUnit_Framework_TestCase {
    public function testExtensionLoaded() {
        $this->assertTrue(extension_loaded('curl'));
    }

    public function testUserAgent() {
        $test = new Test();
        $test->curl->setUserAgent(Curl::USER_AGENT);
        $this->assertTrue($test->server('GET', array(
            'test' => 'server',
            'key' => 'HTTP_USER_AGENT',
        )) === Curl::USER_AGENT);
    }

    public function testGet() {
        $test = new Test();
        $this->assertTrue($test->server('GET', array(
            'test' => 'server',
            'key' => 'REQUEST_METHOD',
        )) === 'GET');
    }

    public function testPostRequestMethod() {
        $test = new Test();
        $this->assertTrue($test->server('POST', array(
            'test' => 'server',
            'key' => 'REQUEST_METHOD',
        )) === 'POST');
    }

    public function testPostData() {
        $test = new Test();
        $this->assertTrue($test->server('POST', array(
            'test' => 'post',
            'key' => 'test',
        )) === 'post');
    }

    public function testPut() {
        $test = new Test();
        $this->assertTrue($test->server('PUT', array(
            'test' => 'server',
            'key' => 'REQUEST_METHOD',
        )) === 'PUT');

        $test = new Test();
        $this->assertTrue($test->server('PUT', array(
            'test' => 'put',
            'key' => 'test',
        )) === 'put');
    }

    public function testDelete() {
        $test = new Test();
        $this->assertTrue($test->server('DELETE', array(
            'test' => 'server',
            'key' => 'REQUEST_METHOD',
        )) === 'DELETE');

        $test = new Test();
        $this->assertTrue($test->server('DELETE', array(
            'test' => 'delete',
            'key' => 'test',
        )) === 'delete');
    }

    public function testBasicHttpAuth() {
        $test = new Test();
        $this->assertTrue($test->server('GET', array(
            'test' => 'http_basic_auth',
        )) === 'canceled');

        $username = 'myusername';
        $password = 'mypassword';
        $test = new Test();
        $test->curl->setBasicAuthentication($username, $password);
        $test->server('GET', array(
            'test' => 'http_basic_auth',
        ));
        $json = json_decode($test->curl->response);
        $this->assertTrue($json->username === $username);
        $this->assertTrue($json->password === $password);
    }

    public function testReferrer() {
        $test = new Test();
        $test->curl->setReferrer('myreferrer');
        $this->assertTrue($test->server('GET', array(
            'test' => 'server',
            'key' => 'HTTP_REFERER',
        )) === 'myreferrer');
    }

    public function testCookies() {
        $test = new Test();
        $test->curl->setCookie('mycookie', 'yum');
        $this->assertTrue($test->server('GET', array(
            'test' => 'cookie',
            'key' => 'mycookie',
        )) === 'yum');
    }

    public function testError() {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $test->curl->get('http://1.2.3.4/');
        $this->assertTrue($test->curl->error === TRUE);
        $this->assertTrue($test->curl->error_code === CURLE_OPERATION_TIMEOUTED);
    }

    public function testHeaders() {
        $test = new Test();
        $test->curl->setHeader('Content-Type', 'application/json');
        $test->curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $test->curl->setHeader('Accept', 'application/json');
        $this->assertTrue($test->server('GET', array(
            'test' => 'server',
            'key' => 'CONTENT_TYPE',
        )) === 'application/json');
        $this->assertTrue($test->server('GET', array(
            'test' => 'server',
            'key' => 'HTTP_X_REQUESTED_WITH',
        )) === 'XMLHttpRequest');
        $this->assertTrue($test->server('GET', array(
            'test' => 'server',
            'key' => 'HTTP_ACCEPT',
        )) === 'application/json');
    }
}
