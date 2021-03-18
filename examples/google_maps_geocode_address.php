<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

$address = 'Paris, France';
$curl = new Curl();
$curl->get('https://maps.googleapis.com/maps/api/geocode/json', [
    'address' => $address,
]);

if ($curl->response->status === 'OK') {
    $result = $curl->response->results['0'];
    echo
        $result->formatted_address . ' is located at ' .
        'latitude ' . $result->geometry->location->lat . ' and ' .
        'longitude ' .  $result->geometry->location->lng . '.';
}
