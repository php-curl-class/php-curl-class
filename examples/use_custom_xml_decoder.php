<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$my_xml_decoder = function ($response) {
    $xml_obj = @simplexml_load_string($response);
    if ($xml_obj !== false) {
        $response = json_decode(json_encode($xml_obj), true);
    }
    return $response;
};

$curl = new Curl();
$curl->setXmlDecoder($my_xml_decoder);
$curl->get('https://httpbin.org/xml');

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
