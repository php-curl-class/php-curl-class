<?php
require '../src/Curl/Curl.php';

use \Curl\Curl;

define('GRATIPAY_USERNAME', 'XXXXXXXXXX');
define('GRATIPAY_API_KEY', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX');

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
$curl->setBasicAuthentication(GRATIPAY_API_KEY);
$curl->post('https://www.gittip.com/' . GRATIPAY_USERNAME . '/tips.json', json_encode($data));

foreach ($curl->response as $tip) {
    echo $tip->amount . ' given to ' . $tip->username . '.' . "\n";
}
