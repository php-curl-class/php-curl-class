<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

// If needed, start a SOCKS 5 proxy tunnel:
//   $ ssh -D 8080 -C -N -v user@example.com

$multi_curl = new MultiCurl();
$multi_curl->setProxy('127.0.0.1:8080');
$multi_curl->setProxyType(CURLPROXY_SOCKS5);
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->complete(function ($instance) {
    var_dump($instance->response);
});
$multi_curl->start();
