<?php
require '../src/Curl/Curl.php';
require '../src/Curl/MultiCurl.php';

use \Curl\Curl;
use \Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->addDelete('https://httpbin.org/delete', array(
    'id' => '123',
));
$multi_curl->addDelete('https://httpbin.org/delete', array(
    'id' => '456',
));

$multi_curl->start();
