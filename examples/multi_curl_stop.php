<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

// Count the number of completed requests.
$request_count = 0;
$multi_curl->complete(function ($instance) use (&$request_count) {
    $request_count += 1;
});

$multi_curl->success(function ($instance) use (&$request_count, $multi_curl) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";

    // Stop pending requests and attempt to stop active requests after the first
    // successful request.
    $multi_curl->stop();
});

$multi_curl->addGet('https://httpbin.org/delay/4');
$multi_curl->addGet('https://httpbin.org/delay/1');
$multi_curl->addGet('https://httpbin.org/delay/3');
$multi_curl->addGet('https://httpbin.org/delay/2');

$multi_curl->start();

assert($request_count === 1);
