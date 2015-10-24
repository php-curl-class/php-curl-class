<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

define('API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXX');
define('API_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('OAUTH_ACCESS_TOKEN', 'XXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('OAUTH_TOKEN_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

$status = 'I love php curl class. https://github.com/php-curl-class/php-curl-class';

$oauth_data = array(
    'oauth_consumer_key' => API_KEY,
    'oauth_nonce' => md5(microtime() .  mt_rand()),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_token' => OAUTH_ACCESS_TOKEN,
    'oauth_version' => '1.0',
    'status' => $status,
);

$url = 'https://api.twitter.com/1.1/statuses/update.json';
$request = implode('&', array(
    'POST',
    rawurlencode($url),
    rawurlencode(http_build_query($oauth_data, '', '&', PHP_QUERY_RFC3986)),
));
$key = implode('&', array(API_SECRET, OAUTH_TOKEN_SECRET));
$oauth_data['oauth_signature'] = base64_encode(hash_hmac('sha1', $request, $key, true));
$data = http_build_query($oauth_data, '', '&');

$curl = new Curl();
$curl->post($url, $data);

echo 'Posted "' . $curl->response->text . '" at ' . $curl->response->created_at . '.' . "\n";
