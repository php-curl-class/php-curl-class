<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\MultiCurl;

$multi_curl = new MultiCurl('https://www.example.com/sites/');

$get_1 = $multi_curl->addGet('page1.html');
assert($get_1->url === 'https://www.example.com/sites/page1.html');

$get_2 = $multi_curl->addGet('page2.html');
assert($get_2->url === 'https://www.example.com/sites/page2.html');

$get_3 = $multi_curl->addGet('page3.html');
assert($get_3->url === 'https://www.example.com/sites/page3.html');

$multi_curl->start();
