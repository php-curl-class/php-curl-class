<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

define('MAILCHIMP_API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-XXX');
$parts = explode('-', MAILCHIMP_API_KEY);
define('MAILCHIMP_BASE_URL', 'https://' . $parts['1'] . '.api.mailchimp.com/2.0/');


$curl = new Curl();
$curl->get(MAILCHIMP_BASE_URL . '/lists/list.json', array(
    'apikey' => MAILCHIMP_API_KEY,
));

if ($curl->response->total === 0) {
    echo 'No lists found';
    exit;
}

$lists = $curl->response->data;
$list = $lists['0'];

$curl->post(MAILCHIMP_BASE_URL . '/lists/subscribe.json', array(
    'apikey' => MAILCHIMP_API_KEY,
    'id' => $list->id,
    'email' => array(
        'email' => 'user@example.com',
    ),
));

if ($curl->error) {
    echo $curl->response->name . ': ' . $curl->response->error . "\n";
} else {
    echo 'Subscribed ' . $curl->response->email . '.' . "\n";
}
