<?php
require '../src/Curl.class.php';


$curl = new Curl();
$curl->get('https://domai.nr/api/json/search', array(
    'q' => 'example',
));

foreach ($curl->response->results as $result) {
    echo $result->domain . ' is ' . $result->availability . '.' . "\n";
}
