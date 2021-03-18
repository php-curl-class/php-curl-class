<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$myfile = curl_file_create('cats.jpg', 'image/jpeg', 'test_name');

$curl = new Curl();

// HINT: If API documentation refers to using something like curl -F "myimage=image.png",
// curl --form "myimage=image.png", or the html form is similar to <form enctype="multipart/form-data" method="post">,
// then try uncommenting the following line:
// $curl->setHeader('Content-Type', 'multipart/form-data');

$curl->post('https://httpbin.org/post', [
    'myfile' => $myfile,
]);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Success' . "\n";
}
