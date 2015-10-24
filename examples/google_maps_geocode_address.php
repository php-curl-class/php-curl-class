<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$address = 'Paris, France';
$curl = new Curl();
$curl->get('http://maps.googleapis.com/maps/api/geocode/json', array(
    'address' => $address,
));

if ($curl->response->status === 'OK') {
    $result = $curl->response->results['0'];
    echo
        $result->formatted_address . ' is located at ' .
        'latitude ' . $result->geometry->location->lat . ' and ' .
        'longitude ' .  $result->geometry->location->lng . '.';
}
