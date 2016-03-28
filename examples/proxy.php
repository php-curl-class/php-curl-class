<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
$curl->setOpt(CURLOPT_PROXY, 'someproxy.com:9999');
$curl->setOpt(CURLOPT_PROXYUSERPWD, 'username:password');
$curl->get('https://httpbin.org/get');
var_dump($curl->response);
