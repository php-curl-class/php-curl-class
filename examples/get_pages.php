<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
for ($i = 1; $i <= 10; $i++) {
    $curl->get('https://httpbin.org/get', [
        'page' => $i,
    ]);
    // TODO: Do something with result $curl->response.
}
