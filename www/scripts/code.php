<?php
$curl = new Curl();
$curl->get('https://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
    $curl->diagnose();
} else {
    echo 'Success! Here is the response:' . "\n";
    var_dump($curl->response);
}
