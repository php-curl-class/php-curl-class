<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->setProxy('someproxy.com', '9999', 'username', 'password');
$curl->setProxyTunnel();
$curl->get('https://httpbin.org/get');
var_dump($curl->response);
