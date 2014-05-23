<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

define('GITTIP_USERNAME', 'XXXXXXXXXX');
define('GITTIP_API_KEY', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX');

$data = array(
    array(
        'username' => 'user' . mt_rand(),
        'platform' => 'gittip',
        'amount' =>  '0.02',
    ),
    array(
        'username' => 'user' . mt_rand(),
        'platform' => 'gittip',
        'amount' =>  '0.02',
    ),
);

$curl = new Curl();
$curl->setHeader('Content-Type', 'application/json');
$curl->setBasicAuthentication(GITTIP_API_KEY);
$curl->post('https://www.gittip.com/' . GITTIP_USERNAME . '/tips.json', json_encode($data));

foreach ($curl->response as $tip) {
    echo $tip->amount . ' given to ' . $tip->username . '.' . "\n";
}
