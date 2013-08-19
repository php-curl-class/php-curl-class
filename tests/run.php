<?php
// Usage: phpunit --verbose run.php

require '../Curl.class.php';

define('BASE_URL', 'https://127.0.0.1/php-curl-class/');

class Test {
    function __construct() {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
    }

    function server($request_method, $test, $key='') {
        $request_method = strtolower($request_method);
        $url = BASE_URL . 'tests/server.php';
        $this->curl->$request_method($url, array(
            'test' => $test,
            'key' => $key,
        ));
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
        $this->assertTrue($test->server('GET', 'server', 'HTTP_USER_AGENT') === Curl::USER_AGENT);
    }

    public function testGet() {
        $test = new Test();
        $this->assertTrue($test->server('GET', 'server', 'REQUEST_METHOD') === 'GET');
    }

    public function testPost() {
        $test = new Test();
        $this->assertTrue($test->server('POST', 'server', 'REQUEST_METHOD') === 'POST');

        $test = new Test();
        $this->assertTrue($test->server('POST', 'post', 'test') === 'post');
    }

    public function testBasicHttpAuth() {
        $test = new Test();
        $this->assertTrue($test->server('GET', 'http_basic_auth') === 'canceled');

        $username = 'myusername';
        $password = 'mypassword';
        $test = new Test();
        $test->curl->setBasicAuthentication($username, $password);
        $test->server('GET', 'http_basic_auth');
        $json = json_decode($test->curl->response);
        $this->assertTrue($json->username === $username);
        $this->assertTrue($json->password === $password);
    }

    public function testReferrer() {
        $test = new Test();
        $test->curl->setReferrer('myreferrer');
        $this->assertTrue($test->server('GET', 'server', 'HTTP_REFERER') === 'myreferrer');
    }

    public function testCookies() {
        $test = new Test();
        $test->curl->setCookie('mycookie', 'yum');
        $this->assertTrue($test->server('GET', 'cookie', 'mycookie') === 'yum');
    }

    public function testError() {
        $test = new Test();
        $test->curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        $test->curl->get('http://1.2.3.4/');
        $this->assertTrue($test->curl->error === TRUE);
        $this->assertTrue($test->curl->error_code === CURLE_OPERATION_TIMEOUTED);
    }
}
