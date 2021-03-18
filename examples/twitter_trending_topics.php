<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const API_KEY = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
const API_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
const OAUTH_ACCESS_TOKEN = 'XXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
const OAUTH_TOKEN_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$woeid = '2487956';

$oauth_data = [
    'id' => $woeid,
    'oauth_consumer_key' => API_KEY,
    'oauth_nonce' => md5(microtime() .  mt_rand()),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_token' => OAUTH_ACCESS_TOKEN,
    'oauth_version' => '1.0',
];

$request_values = $oauth_data;
ksort($request_values);

$url = 'https://api.twitter.com/1.1/trends/place.json';
$request = implode('&', [
    'GET',
    rawurlencode($url),
    rawurlencode(http_build_query($request_values, '', '&', PHP_QUERY_RFC3986)),
]);
$key = implode('&', [rawurlencode(API_SECRET), rawurlencode(OAUTH_TOKEN_SECRET)]);
$oauth_data['oauth_signature'] = base64_encode(hash_hmac('sha1', $request, $key, true));

$authorization = [];
foreach ($oauth_data as $key => $value) {
    $authorization[] = $key . '="' . rawurlencode($value) . '"';
}
$authorization = 'Authorization: OAuth ' . implode(', ', $authorization);

$curl = new Curl();
$curl->setOpt(CURLOPT_HTTPHEADER, [$authorization]);
$curl->get($url, [
    'id' => $woeid,
]);

echo 'Current trends:' . "\n";
foreach ($curl->response['0']->trends as $trend) {
    echo '- ' . $trend->name . "\n";
}
