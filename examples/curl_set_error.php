<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$max_retries = 5;

$curl = new Curl();
$curl->setRetry($max_retries);

$curl->beforeSend(function ($instance) {
    echo 'about to make request to ' . $instance->url . "\n";
});

$curl->error(function ($instance) {
    echo 'not lucky this round' . "\n";
});

$curl->success(function ($instance) {
    echo
        'success!' . "\n" .
        'got number ' . $instance->response->args->number . ' ' .
        'after ' . $instance->attempts . ' attempt(s).' . "\n";
});

$curl->setError(function ($instance) {
    $random_number = (int)$instance->response->args->number;
    $lucky = $random_number === 7;
    $instance->error = !$lucky;

    if (!$lucky) {
        $instance->setUrl('https://httpbin.org/get?number=' . random_int(0, 10));
    }
});

$curl->get('https://httpbin.org/get?number=' . random_int(0, 10));

// $ php curl_set_error.php
// about to make request to https://httpbin.org/get?number=3
// about to make request to https://httpbin.org/get?number=1
// about to make request to https://httpbin.org/get?number=7
// success!
// got number 7 after 3 attempt(s).
