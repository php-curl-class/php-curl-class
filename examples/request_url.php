<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

// make a request to Dubizzle website.


$curl = new Curl();
$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
$curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
$curl->setOpt(CURLOPT_TIMEOUT, 0);
$params = array(
    "site" => "--",
    "s" => "MT",
    "rc" => "140",
    "c1" => "1584",
    "min_year" => '2007',
    "max_year" => '2007',
    "num_results" => 'all',
    "added_days"=>'all'
);
$curl->get('https://uae.dubizzle.com/search/', $params);

echo "Original Request URL:    $curl->url";
echo "<br/><br/>";
echo "Redirect Request URL:       ".$curl->getRequestUrl();
