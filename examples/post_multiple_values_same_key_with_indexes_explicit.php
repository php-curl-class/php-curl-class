<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl "https://httpbin.org/post" -d "foo[0]=bar&foo[1]=baz"

$curl = new Curl();
$curl->post('https://httpbin.org/post', [
    'foo[0]' => 'bar',
    'foo[1]' => 'baz',
]);
