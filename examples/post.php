<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

// curl \
//     -X POST \
//     -d "id=1&content=Hello+world%21&date=2015-06-30+19%3A42%3A21" \
//     "https://httpbin.org/post"

$data = array(
    'id' => '1',
    'content' => 'Hello world!',
    'date' => date('Y-m-d H:i:s'),
);

$curl = new Curl();
$curl->post('https://httpbin.org/post', $data);
var_dump($curl->response->form);
