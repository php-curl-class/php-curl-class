<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;
use Curl\MultiCurl;

$multi_curl = new MultiCurl();
$multi_curl->complete(function ($instance) {
    echo 'call to "' . $instance->url . '" completed.' . "\n";
});

$curl_1 = new Curl();
$curl_1->setOpt(CURLOPT_POST, true);
$curl_1->setOpt(CURLOPT_POSTFIELDS, [
    'to' => 'alice',
    'subject' => 'hi',
    'body' => 'hi Alice',
]);
$curl_1->setUrl('https://httpbin.org/post');
$multi_curl->addCurl($curl_1);

$curl_2 = new Curl();
$curl_2->setOpt(CURLOPT_POST, true);
$curl_2->setOpt(CURLOPT_POSTFIELDS, [
    'to' => 'bob',
    'subject' => 'hi',
    'body' => 'hi Bob',
]);
$curl_2->setUrl('https://httpbin.org/post');
$multi_curl->addCurl($curl_2);

$multi_curl->start();
