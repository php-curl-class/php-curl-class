<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\MultiCurl;

$multi_curl = new MultiCurl();
$multi_curl->setProxy('someproxy.com', '9999', 'username', 'password');
$multi_curl->setProxyTunnel();
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->complete(function ($instance) {
    var_dump($instance->response);
});
$multi_curl->start();
