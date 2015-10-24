<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

define('API_KEY', '');
define('API_SECRET', '');

$url = 'https://coinbase.com/api/v1/account/balance';

$nonce = (int)(microtime(true) * 1e6);
$message = $nonce . $url;
$signature = hash_hmac('sha256', $message, API_SECRET);

$curl = new Curl();
$curl->setHeader('ACCESS_KEY', API_KEY);
$curl->setHeader('ACCESS_SIGNATURE', $signature);
$curl->setHeader('ACCESS_NONCE', $nonce);
$curl->get($url);

echo
    'My current account balance at Coinbase is ' .
    $curl->response->amount . ' ' . $curl->response->currency . '.' . "\n";
