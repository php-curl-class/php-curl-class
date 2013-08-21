<?php
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
$data_values = $request_method === 'POST' ? $_POST : $_GET;
$test = isset($data_values['test']) ? $data_values['test'] : '';
$key = isset($data_values['key']) ? $data_values['key'] : '';

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
else if ($test === 'put') {
    header('Content-Type: text/plain');
    $value = isset($_GET[$key]) ? $_GET[$key] : '';
    echo $value;
}
else if ($test === 'post') {
    header('Content-Type: text/plain');
    $value = isset($_POST[$key]) ? $_POST[$key] : '';
    echo $value;
}
else if ($test === 'server') {
    header('Content-Type: text/plain');
    $value = isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    echo $value;
}
else if ($test === 'cookie') {
    header('Content-Type: text/plain');
    $value = isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
    echo $value;
}
