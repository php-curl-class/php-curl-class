<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl "https://httpbin.org/post" -d "foo[]=bar&foo[]=baz"

$curl = new Curl();
$curl->post('https://httpbin.org/post', [
    'foo' => [
        'bar',
        'baz',
    ],
]);
