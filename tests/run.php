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

    function server($request_method, $test, $key) {
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
    }
}
