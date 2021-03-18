<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const MAILCHIMP_API_KEY = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-XXX';
$parts = explode('-', MAILCHIMP_API_KEY);
$MAILCHIMP_BASE_URL = 'https://' . $parts['1'] . '.api.mailchimp.com/2.0/';


$curl = new Curl();
$curl->get($MAILCHIMP_BASE_URL . '/lists/list.json', [
    'apikey' => MAILCHIMP_API_KEY,
]);

if ($curl->response->total === 0) {
    echo 'No lists found';
    exit;
}

$lists = $curl->response->data;
$list = $lists['0'];

$curl->post($MAILCHIMP_BASE_URL . '/lists/subscribe.json', [
    'apikey' => MAILCHIMP_API_KEY,
    'id' => $list->id,
    'email' => [
        'email' => 'user@example.com',
    ],
]);

if ($curl->error) {
    echo $curl->response->name . ': ' . $curl->response->error . "\n";
} else {
    echo 'Subscribed ' . $curl->response->email . '.' . "\n";
}
