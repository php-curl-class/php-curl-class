<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('https://api/domainr.com/v1/search', array(
    'client_id' => 'php_curl_class',
    'q' => 'example',
));

foreach ($curl->response->results as $result) {
    echo $result->domain . ' is ' . $result->availability . '.' . "\n";
}
