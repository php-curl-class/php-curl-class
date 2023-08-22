<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$max_retries = 5;

$multi_curl = new MultiCurl();
$multi_curl->setRetry($max_retries);

$multi_curl->beforeSend(function ($instance) {
    echo 'about to make request to ' . $instance->url . "\n";
});

$multi_curl->error(function ($instance) {
    echo 'not lucky this round' . "\n";
});

$multi_curl->success(function ($instance) {
    echo
        'success!' . "\n" .
        'got number ' . $instance->response->args->number . ' ' .
        'after ' . $instance->attempts . ' attempt(s).' . "\n";
});

$multi_curl->setError(function ($instance) {
    $random_number = (int)$instance->response->args->number;
    $lucky = $random_number === 7;
    $instance->error = !$lucky;

    if (!$lucky) {
        $instance->setUrl('https://httpbin.org/get?number=' . random_int(0, 10));
    }
});

$multi_curl->addGet('https://httpbin.org/get?number=' . random_int(0, 10));
$multi_curl->start();

// $ php multi_curl_set_error.php
// about to make request to https://httpbin.org/get?number=4
// about to make request to https://httpbin.org/get?number=7
// success!
// got number 7 after 2 attempt(s).
