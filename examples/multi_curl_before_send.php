<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\MultiCurl;

$headers = array(
    'Content-Type' => 'application/json',
    'X-CUSTOM-HEADER' => 'my-custom-header',
);

$multi_curl = new MultiCurl();

$multi_curl->beforeSend(function ($instance) use ($headers) {
    foreach ($headers as $key => $value) {
        $instance->setHeader($key, $value);
    }
});

$multi_curl->addGet('https://www.example.com/');
$multi_curl->addGet('https://www.example.org/');
$multi_curl->addGet('https://www.example.net/');

$multi_curl->start();
