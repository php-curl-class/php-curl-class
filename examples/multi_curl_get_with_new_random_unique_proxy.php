<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\ArrayUtil;
use Curl\MultiCurl;

$proxies = [
    'someproxy.com:9999',
    'someproxy.com:80',
    'someproxy.com:443',
];
$current_proxies = $proxies;

$multi_curl = new MultiCurl();
$multi_curl->setProxyType(CURLPROXY_SOCKS5);
$multi_curl->beforeSend(function ($instance) use ($proxies, &$current_proxies) {
    if (!count($current_proxies)) {
        $current_proxies = $proxies;
    }

    // Use random proxy that hasn't been used yet.
    $rand_key = ArrayUtil::arrayRandomIndex($current_proxies);
    $new_random_proxy = $current_proxies[$rand_key];
    $instance->setProxy($new_random_proxy);

    // Remove proxy from list of current proxies.
    unset($current_proxies[$rand_key]);

    // Re-index list of current proxies.
    $current_proxies = array_values($current_proxies);

    echo 'about to make request ' . $instance->id . ' using proxy "' . $new_random_proxy . '".' . "\n";
});

$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/ip');

$multi_curl->complete(function ($instance) {
    echo
        'curl id ' . $instance->id . ' completed:' . "\n" .
        '- ip: ' . $instance->response->origin . "\n" .
        '- proxy: ' . $instance->getOpt(CURLOPT_PROXY) . "\n" .
        '- url: ' . $instance->effectiveUrl . '' . "\n" .
        '';
});

$multi_curl->start();
