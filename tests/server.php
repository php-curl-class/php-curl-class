<?php
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

$var = '_' . strtoupper($request_method);
$var = $$var;

$test = isset($var['test']) ? $var['test'] : '';

if ($test == 'http_basic_auth') {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'canceled';
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(array(
        'username' => $_SERVER['PHP_AUTH_USER'],
        'password' => $_SERVER['PHP_AUTH_PW'],
    ));
    exit;
}

header('Content-Type: text/plain');
$test = '_' . strtoupper($var['test']);
$test = $$test;
//var_dump('test:', $test);

$key = isset($var['key']) ? $var['key'] : '';
//var_dump('key:', $key);

$value = isset($test[$key]) ? $test[$key] : '';
//var_dump('value:', $value);

echo $value;
