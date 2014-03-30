<?php
require '../src/Curl.class.php';


$curl = new Curl();
$curl->get('https://coinbase.com/api/v1/prices/spot_rate');

echo
    'The current price of bitcoin at Coinbase is ' .
    '$' . $curl->response->amount . ' ' . $curl->response->currency . '.' . "\n";
