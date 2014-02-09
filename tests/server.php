<?php
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
$data_values = $request_method === 'POST' ? $_POST : $_GET;
$test = isset($_SERVER['HTTP_X_DEBUG_TEST']) ? $_SERVER['HTTP_X_DEBUG_TEST'] : '';
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
else if ($test === 'get') {
    echo http_build_query($_GET);
    exit;
}
else if ($test === 'post') {
    echo http_build_query($_POST);
    exit;
}
else if ($test === 'post_multidimensional' || $test === 'put') {
    $http_raw_post_data = file_get_contents('php://input');
    echo $http_raw_post_data;
    exit;
}
else if ($test === 'post_file_path_upload') {
    echo mime_content_type($_FILES[$key]['tmp_name']);
    exit;
}
else if ($test === 'put_file_handle') {
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, file_get_contents('php://input'));
    echo mime_content_type($tmp_filename);
    unlink($tmp_filename);
    exit;
}
else if ($test === 'request_method') {
    echo $request_method;
    exit;
}
else if ($test === 'request_uri') {
    echo $_SERVER['REQUEST_URI'];
    exit;
}
else if ($test === 'cookiejar') {
    setcookie('mycookie', 'yum');
    exit;
}

header('Content-Type: text/plain');

$data_mapping = array(
    'cookie' => '_COOKIE',
    'delete' => '_GET',
    'post' => '_POST',
    'put' => '_GET',
    'server' => '_SERVER',
);

$data = $$data_mapping[$test];
$value = isset($data[$key]) ? $data[$key] : '';
echo $value;
