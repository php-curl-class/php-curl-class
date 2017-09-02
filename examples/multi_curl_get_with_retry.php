<?php
require __DIR__ . '/../vendor/autoload.php';

use \Curl\MultiCurl;

$max_retries = 3;

$multi_curl = new MultiCurl();
$multi_curl->setRetry($max_retries);
$multi_curl->complete(function ($instance) {
    echo 'call to "' . $instance->url . '" completed.' . "\n";
    echo 'attempts: ' . $instance->attempts . "\n";
    echo 'retries: ' . $instance->retries . "\n";
});

$multi_curl->addGet('https://httpbin.org/status/503?a');
$multi_curl->addGet('https://httpbin.org/status/503?b');
$multi_curl->addGet('https://httpbin.org/status/503?c');

$multi_curl->start();
