<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

// Retrieve first N pages of search results.
$pages = 10;
$q = 'coffee';

$curl = new Curl();
$curl->setUrl('https://www.example.com/search');

for ($i = 1; $i <= $pages; $i++) {
    // https://www.example.com/search?q=coffee&page=N
    $curl->get([
        'q' => $q,
        'page' => $i,
    ]);
}
