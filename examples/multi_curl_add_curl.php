<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;
use Curl\MultiCurl;

$multi_curl = new MultiCurl();
$multi_curl->complete(function ($instance) {
    echo 'call to "' . $instance->url . '" completed.' . "\n";
});

$curl_1 = new Curl();
$curl_1->setPost('https://httpbin.org/post', [
    'to' => 'alice',
    'subject' => 'hi',
    'body' => 'hi Alice',
]);
$multi_curl->addCurl($curl_1);

$curl_2 = new Curl();
$curl_2->setPost('https://httpbin.org/post', [
    'to' => 'bob',
    'subject' => 'hi',
    'body' => 'hi Bob',
]);
$multi_curl->addCurl($curl_2);

$multi_curl->start();
