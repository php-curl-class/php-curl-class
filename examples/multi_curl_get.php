<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

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
