<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->addPut('https://httpbin.org/put', [
    'id' => '123',
    'subject' => 'hello',
    'body' => 'hello',
]);
$multi_curl->addPut('https://httpbin.org/put', [
    'id' => '456',
    'subject' => 'hello',
    'body' => 'hello',
]);

$multi_curl->start();
