<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const GRATIPAY_USERNAME = 'XXXXXXXXXX';
const GRATIPAY_API_KEY = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX';

$data = [
    [
        'username' => 'user' . mt_rand(),
        'platform' => 'gratipay',
        'amount' =>  '0.02',
    ],
    [
        'username' => 'user' . mt_rand(),
        'platform' => 'gratipay',
        'amount' =>  '0.02',
    ],
];

$curl = new Curl();
$curl->setHeader('Content-Type', 'application/json');
$curl->setBasicAuthentication(GRATIPAY_API_KEY);
$curl->post('https://gratipay.com/' . GRATIPAY_USERNAME . '/tips.json', $data);

foreach ($curl->response as $tip) {
    echo $tip->amount . ' given to ' . $tip->username . '.' . "\n";
}
