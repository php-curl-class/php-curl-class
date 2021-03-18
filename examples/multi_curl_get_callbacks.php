<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->success(function ($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
    echo 'response: ' . $instance->response . "\n";
});
$multi_curl->error(function ($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});
$multi_curl->complete(function ($instance) {
    echo 'call to "' . $instance->url . '" completed.' . "\n";
});

$multi_curl->addGet('https://www.google.com/search', [
    'q' => 'hello world',
]);
$multi_curl->addGet('https://duckduckgo.com/', [
    'q' => 'hello world',
]);
$multi_curl->addGet('https://www.bing.com/search', [
    'q' => 'hello world',
]);

$multi_curl->start();
