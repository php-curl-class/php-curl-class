<?php
require '../src/Curl/Curl.php';
require '../src/Curl/MultiCurl.php';

use \Curl\Curl;
use \Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->addPatch('https://httpbin.org/patch', array(
    'id' => '123',
    'body' => 'hello world!',
));
$multi_curl->addPatch('https://httpbin.org/patch', array(
    'id' => '456',
    'body' => 'hello world!',
));

$multi_curl->start();
