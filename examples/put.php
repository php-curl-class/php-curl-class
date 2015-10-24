<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

// curl -X PUT -d "id=1&first_name=Zach&last_name=Borboa" "https://httpbin.org/put"
$curl = new Curl();
$curl->put('https://httpbin.org/put', array(
    'id' => 1,
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
));

echo 'Data server received via PUT:' . "\n";
var_dump($curl->response->form);
