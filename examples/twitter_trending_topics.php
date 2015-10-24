<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

define('API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXX');
define('API_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('OAUTH_ACCESS_TOKEN', 'XXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('OAUTH_TOKEN_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

$woeid = '2487956';

$oauth_data = array(
    'id' => $woeid,
    'oauth_consumer_key' => API_KEY,
    'oauth_nonce' => md5(microtime() .  mt_rand()),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_token' => OAUTH_ACCESS_TOKEN,
    'oauth_version' => '1.0',
);

$request_values = $oauth_data;
ksort($request_values);

$url = 'https://api.twitter.com/1.1/trends/place.json';
$request = implode('&', array(
    'GET',
    rawurlencode($url),
    rawurlencode(http_build_query($request_values, '', '&', PHP_QUERY_RFC3986)),
));
$key = implode('&', array(rawurlencode(API_SECRET), rawurlencode(OAUTH_TOKEN_SECRET)));
$oauth_data['oauth_signature'] = base64_encode(hash_hmac('sha1', $request, $key, true));

$authorization = array();
foreach ($oauth_data as $key => $value) {
    $authorization[] = $key . '="' . rawurlencode($value) . '"';
}
$authorization = 'Authorization: OAuth ' . implode(', ', $authorization);

$curl = new Curl();
$curl->setOpt(CURLOPT_HTTPHEADER, array($authorization));
$curl->get($url, array(
    'id' => $woeid,
));

echo 'Current trends:' . "\n";
foreach ($curl->response['0']->trends as $trend) {
    echo '- ' . $trend->name . "\n";
}
