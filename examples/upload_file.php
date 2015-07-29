<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

$myfile = curl_file_create('cats.jpg', 'image/png', 'test_name');

$curl = new Curl();
$curl->post('https://httpbin.org/post', array(
    'myfile' => $myfile,
));

if ($curl->error) {
    echo 'Error: ' . $curl->error_message . "\n";
} else {
    echo 'Success' . "\n";
}
