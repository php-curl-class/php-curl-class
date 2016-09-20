<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('https://httpbin.org/get');

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
