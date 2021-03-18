<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl --request POST "https://httpbin.org/post" --data "id=1&content=Hello+world%21&date=2015-06-30+19%3A42%3A21"

$curl = new Curl();
$curl->post('https://httpbin.org/post', [
    'id' => '1',
    'content' => 'Hello world!',
    'date' => date('Y-m-d H:i:s'),
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Data server received via POST:' . "\n";
    var_dump($curl->response->form);
}
