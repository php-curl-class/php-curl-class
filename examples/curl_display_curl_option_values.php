<?php

require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->setUserAgent('some agent');
$curl->setTimeout(60);

foreach ($curl->getOptions() as $option => $value) {
    echo 'option ' . $option . ':' . "\n";
    $curl->displayCurlOptionValue($option, $value);
    echo "\n";
}

// option 181:
// CURLOPT_PROTOCOLS: 3 (CURLPROTO_HTTP | CURLPROTO_HTTPS)
//
// option 182:
// CURLOPT_REDIR_PROTOCOLS: 3 (CURLPROTO_HTTP | CURLPROTO_HTTPS)
//
// option 10018:
// CURLOPT_USERAGENT: some agent
//
// option 13:
// CURLOPT_TIMEOUT: 60
//
// option 2:
// CURLINFO_HEADER_OUT: true
//
// option 20056:
// CURLOPT_PROGRESSFUNCTION: (callable)
//
// option 43:
// CURLOPT_NOPROGRESS: false
//
// option 20079:
// CURLOPT_HEADERFUNCTION: (callable)
//
// option 19913:
// CURLOPT_RETURNTRANSFER: true
