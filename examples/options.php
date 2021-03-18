<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl --get --request OPTIONS "http://127.0.0.1:8000/" --data "foo=bar"

$curl = new Curl();
$curl->options('http://127.0.0.1:8000/', [
    'foo' => 'bar',
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
