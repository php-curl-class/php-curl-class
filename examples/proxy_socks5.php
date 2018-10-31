<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

// If needed, start a SOCKS 5 proxy tunnel:
//   $ ssh -D 8080 -C -N -v user@example.com

$curl = new Curl();
$curl->setProxy('127.0.0.1:8080');
$curl->setProxyType(CURLPROXY_SOCKS5);
$curl->get('https://httpbin.org/ip');
var_dump($curl->response);
