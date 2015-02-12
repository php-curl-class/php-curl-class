<?php
require '../src/Curl/Curl.php';
require '../src/Curl/MultiCurl.php';

use \Curl\Curl;
use \Curl\MultiCurl;

$multi_curl = new MultiCurl();

$multi_curl->addPost('https://httpbin.org/post', array(
    'to' => 'alice',
    'subject' => 'hi',
    'body' => 'hi Alice',
));
$multi_curl->addPost('https://httpbin.org/post', array(
    'to' => 'bob',
    'subject' => 'hi',
    'body' => 'hi Bob',
));

$multi_curl->start();
