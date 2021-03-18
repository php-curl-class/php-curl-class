<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl();

// HINT: If API documentation refers to using something like curl -F "myimage=image.png",
// curl --form "myimage=image.png", or the html form is similar to <form enctype="multipart/form-data" method="post">,
// then try uncommenting the following line:
// $multi_curl->setHeader('Content-Type', 'multipart/form-data');

$multi_curl->addPost('https://httpbin.org/post', [
    'image' => new CURLFile('the-lorax.jpg'),
]);

$multi_curl->addPost('https://httpbin.org/post', [
    'image' => new CURLFile('swomee-swans.jpg'),
]);

$multi_curl->addPost('https://httpbin.org/post', [
    'image' => new CURLFile('truffula-trees.jpg'),
]);

$multi_curl->start();
