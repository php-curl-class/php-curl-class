<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('https://coinbase.com/api/v1/prices/spot_rate');

echo
    'The current price of bitcoin at Coinbase is ' .
    '$' . $curl->response->amount . ' ' . $curl->response->currency . '.' . "\n";
