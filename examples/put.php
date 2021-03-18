<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// curl --request PUT "https://httpbin.org/put" --data "id=1&first_name=Zach&last_name=Borboa"

$curl = new Curl();
$curl->put('https://httpbin.org/put', [
    'id' => '1',
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Data server received via PUT:' . "\n";
    var_dump($curl->response->form);
}
