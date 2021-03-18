<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl \
//     -X POST \
//     -H 'Content-Type: application/json' \
//     -d '{"id":"1","content":"Hello world!","date":"2015-06-30 19:42:21"}' \
//     "https://httpbin.org/post"

$data = [
    'id' => '1',
    'content' => 'Hello world!',
    'date' => date('Y-m-d H:i:s'),
];

$curl = new Curl();
$curl->setHeader('Content-Type', 'application/json');
$curl->post('https://httpbin.org/post', $data);
var_dump($curl->response->json);
