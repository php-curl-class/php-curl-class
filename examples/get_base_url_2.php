<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->setUrl('https://httpbin.org/get');
for ($i = 1; $i <= 10; $i++) {
    $curl->get([
        'page' => $i,
    ]);
    // TODO: Do something with result $curl->response.
}
