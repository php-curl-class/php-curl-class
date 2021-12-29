<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

// Pages to fetch.
$urls = [
    'https://www.example1.com/',
    'https://www.example2.com/',
    'https://www.example3.com/',
];

// Array to hold responses.
$responses = [];

$multi_curl = new MultiCurl();
foreach ($urls as $url) {
    $multi_curl->addGet($url);
}
$multi_curl->complete(function ($instance) use (&$responses) {
    // Store responses.
    $responses[] = $instance->response;

    // Alternatively, process each response here inside the callback as it is received.
});
$multi_curl->start();

// Process responses.
foreach ($responses as $response) {
    var_dump($response);
}
