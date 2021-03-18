<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$urls = [
    'tag3' => 'https://httpbin.org/post',
    'tag4' => 'https://httpbin.org/get',
    'tag5' => 'https://httpbin.org/html',
];

$multi_curl = new MultiCurl();

$multi_curl->success(function ($instance) {
    echo 'call to ' . $instance->id . ' with "' . $instance->myTag . '" was successful.' . "\n";
});
$multi_curl->error(function ($instance) {
    echo 'call to ' . $instance->id . ' with "' . $instance->myTag . '" was unsuccessful.' . "\n";
});
$multi_curl->complete(function ($instance) {
    echo 'call to ' . $instance->id . ' with "' . $instance->myTag . '" completed.' . "\n";
});

foreach ($urls as $tag => $url) {
    $instance = $multi_curl->addGet($url);
    $instance->myTag = $tag;
}

$multi_curl->start();
