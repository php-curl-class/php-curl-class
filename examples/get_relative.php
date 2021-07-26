<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl('https://www.example.com/api/');

// https://www.example.com/api/test?key=value
$response = $curl->get('test', [
    'key' => 'value',
]);
assert($curl->url === 'https://www.example.com/api/test?key=value');
assert($curl->url === $curl->effectiveUrl);

// https://www.example.com/root?key=value
$response = $curl->get('/root', [
    'key' => 'value',
]);
assert($curl->url === 'https://www.example.com/root?key=value');
assert($curl->url === $curl->effectiveUrl);
