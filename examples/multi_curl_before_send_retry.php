<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$max_retries = 3;

$multi_curl = new MultiCurl();
$multi_curl->setRetry($max_retries);

$multi_curl->beforeSend(function ($instance) {
    echo 'current attempts: ' . $instance->attempts . "\n";
    echo 'current retries: ' . $instance->retries . "\n";
    echo 'about to make request to ' . $instance->url . "\n";
});

$multi_curl->complete(function ($instance) {
    if ($instance->error) {
        echo 'Error: ' . $instance->errorMessage . "\n";
        echo 'final attempts: ' . $instance->attempts . "\n";
        echo 'final retries: ' . $instance->retries . "\n";
    } else {
        echo 'Response:' . "\n";
        var_dump($instance->response);
    }
});

$multi_curl->addGet('https://httpbin.org/status/503');

$multi_curl->start();
