<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->beforeSend(function ($instance) {
    echo 'about to make request ' . $instance->id . ': "' . $instance->url . '".' . "\n";
});

$multi_curl->success(function ($instance) use ($multi_curl) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";

    // Stop pending requests and attempt to stop active requests after the first
    // successful request.
    $multi_curl->stop();
});

$multi_curl->error(function ($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
});

// Count the number of completed requests.
$request_count = 0;
$multi_curl->complete(function ($instance) use (&$request_count) {
    echo 'call to "' . $instance->url . '" completed.' . "\n";
    $request_count += 1;
});

$multi_curl->addGet('https://httpbin.org/delay/4');
$multi_curl->addGet('https://httpbin.org/delay/1');
$multi_curl->addGet('https://httpbin.org/delay/3');
$multi_curl->addGet('https://httpbin.org/delay/2');

$multi_curl->start();

assert($request_count === 1);
