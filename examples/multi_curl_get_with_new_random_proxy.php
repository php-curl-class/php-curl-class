<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\ArrayUtil;
use Curl\MultiCurl;

$proxies = [
    'someproxy.com:9999',
    'someproxy.com:80',
    'someproxy.com:443',
];
$max_retries = 3;

$multi_curl = new MultiCurl();
$multi_curl->setProxyType(CURLPROXY_SOCKS5);
$multi_curl->setProxies($proxies);

$multi_curl->setRetry(function ($instance) use ($proxies, $max_retries) {
    if ($instance->retries < $max_retries) {
        $new_random_proxy = ArrayUtil::arrayRandom($proxies);
        $instance->setProxy($new_random_proxy);
        return true;
    } else {
        return false;
    }
});

$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');

$multi_curl->complete(function ($instance) {
    echo
        'curl id ' . $instance->id . ':' . "\n" .
        '- ip: ' . $instance->response->origin . "\n" .
        '- proxy: ' . $instance->getOpt(CURLOPT_PROXY) . "\n" .
        '- url: ' . $instance->effectiveUrl . '' . "\n" .
        '';
});

$multi_curl->start();
