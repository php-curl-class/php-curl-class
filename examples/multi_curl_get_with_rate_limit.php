<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$start_time = microtime(true);

$multi_curl = new MultiCurl();
$multi_curl->setRateLimit('2/10s');

$multi_curl->beforeSend(function ($instance) use ($start_time) {
    echo
        sprintf('%.6f', round(microtime(true) - $start_time, 6)) . ' - ' .
        'request ' . $instance->id . ' start' . "\n";
});

$multi_curl->success(function ($instance) use ($start_time) {
    echo
        sprintf('%.6f', round(microtime(true) - $start_time, 6)) . ' - ' .
        'request ' . $instance->id . ' successful (' . $instance->url . ')' . "\n";
});
$multi_curl->error(function ($instance) use ($start_time) {
    echo
        sprintf('%.6f', round(microtime(true) - $start_time, 6)) . ' - ' .
        'request ' . $instance->id . ' unsuccessful (' . $instance->url . ')' . "\n";
});

$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/status/503');

$multi_curl->addGet('https://httpbin.org/ip');
$multi_curl->addGet('https://httpbin.org/status/503');

$multi_curl->addGet('https://httpbin.org/ip');

$multi_curl->start();

// $ php multi_curl_get_with_rate_limit.php
// 0.021839 - request 0 start
// 0.021894 - request 1 start
// 0.661308 - request 0 successful (https://httpbin.org/ip)
// 0.661968 - request 1 unsuccessful (https://httpbin.org/status/503)
// 10.024627 - request 2 start
// 10.024694 - request 3 start
// 10.114304 - request 2 successful (https://httpbin.org/ip)
// 10.117299 - request 3 unsuccessful (https://httpbin.org/status/503)
// 20.029945 - request 4 start
// 20.112836 - request 4 successful (https://httpbin.org/ip)
