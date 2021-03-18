<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->addPost('https://httpbin.org/post', [
    'to' => 'alice',
    'subject' => 'hi',
    'body' => 'hi Alice',
]);
$multi_curl->addPost('https://httpbin.org/post', [
    'to' => 'bob',
    'subject' => 'hi',
    'body' => 'hi Bob',
]);

$multi_curl->start();
