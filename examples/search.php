<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl --request SEARCH "http://127.0.0.1:8000/" --data "a=1&b=2&c=3"

$curl = new Curl();
$curl->search('http://127.0.0.1:8000/', [
    'a' => '1',
    'b' => '2',
    'c' => '3',
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
