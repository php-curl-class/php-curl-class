<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->addDelete('https://httpbin.org/delete', array(
    'id' => '123',
));
$multi_curl->addDelete('https://httpbin.org/delete', array(
    'id' => '456',
));

$multi_curl->start();
