<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();
$multi_curl->setProxies([
    'someproxy.com:9999',
    'someproxy.com:80',
    'someproxy.com:443',
    'someproxy.com:1080',
    'someproxy.com:3128',
    'someproxy.com:8080',
]);
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->complete(function ($instance) {
    echo
        'curl id ' . $instance->id . ' used proxy ' .
        $instance->getOpt(CURLOPT_PROXY) . ' and ' .
        'ip is ' . $instance->response->origin . "\n";
});
$multi_curl->start();
