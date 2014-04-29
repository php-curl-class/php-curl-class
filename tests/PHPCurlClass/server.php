<?php
$http_raw_post_data = file_get_contents('php://input');
$_PUT = array();
$_PATCH = array();

$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
if (!array_key_exists('CONTENT_TYPE', $_SERVER) && array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
    $_SERVER['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
}
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
$data_values = $_GET;
if ($request_method === 'POST') {
    $data_values = $_POST;
}
else if ($request_method === 'PUT') {
    if (strpos($content_type, 'application/x-www-form-urlencoded') === 0) {
        parse_str($http_raw_post_data, $_PUT);
        $data_values = $_PUT;
    }
}
else if ($request_method === 'PATCH') {
    if (strpos($content_type, 'application/x-www-form-urlencoded') === 0) {
        parse_str($http_raw_post_data, $_PATCH);
        $data_values = $_PATCH;
    }
}

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
else if ($test === 'put') {
    echo $http_raw_post_data;
    exit;
}
else if ($test === 'post_multidimensional') {
    echo $http_raw_post_data;
    exit;
}
else if ($test === 'post_file_path_upload') {
    echo mime_content_type($_FILES[$key]['tmp_name']);
    exit;
}
else if ($test === 'put_file_handle') {
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, $http_raw_post_data);
    echo mime_content_type($tmp_filename);
    unlink($tmp_filename);
    exit;
}
else if ($test === 'request_method') {
    header('X-REQUEST-METHOD: ' . $request_method);
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
else if ($test === 'multiple_cookie') {
    setcookie('cookie1', 'scrumptious');
    setcookie('cookie2', 'mouthwatering');
    exit;
}
else if ($test === 'response_header') {
    header('Content-Type: application/json');
    header('ETag: ' . md5('worldpeace'));
    exit;
}
else if ($test === 'json_response') {
    $key = $_POST['key'];
    $value = $_POST['value'];
    header($key . ': ' . $value);
    echo json_encode(array(
        'null' => null,
        'true' => true,
        'false' => false,
        'integer' => 1,
        'float' => 3.14,
        'empty' => '',
        'string' => 'string',
    ));
    exit;
}
else if ($test === 'xml_response') {
    $key = $_POST['key'];
    $value = $_POST['value'];
    header($key . ': ' . $value);
    $doc = new DOMDocument();
    $doc->formatOutput = true;
    $rss = $doc->appendChild($doc->createElement('rss'));
    $rss->setAttribute('version', '2.0');
    $channel = $doc->createElement('channel');
    $title = $doc->createElement('title');
    $title->appendChild($doc->createTextNode('Title'));
    $channel->appendChild($title);
    $link = $doc->createElement('link');
    $link->appendChild($doc->createTextNode('Link'));
    $channel->appendChild($link);
    $description = $doc->createElement('description');
    $description->appendChild($doc->createTextNode('Description'));
    $channel->appendChild($description);
    $rss->appendChild($channel);
    echo $doc->saveXML();
    exit;
}
else if ($test === 'error_message') {
    if (function_exists('http_response_code')) {
        http_response_code(401);
    }
    else {
        header('HTTP/1.1 401 Unauthorized');
    }
    exit;
}
else if ($test === 'redirect') {
    if (!isset($_GET['redirect'])) {
        header('Location: ?redirect');
        exit;
    }

    echo 'OK';
    exit;
}

header('Content-Type: text/plain');

$data_mapping = array(
    'cookie' => '_COOKIE',
    'delete' => '_GET',
    'get' => '_GET',
    'patch' => '_PATCH',
    'post' => '_POST',
    'put' => '_PUT',
    'server' => '_SERVER',
);

$data = $$data_mapping[$test];
$value = isset($data[$key]) ? $data[$key] : '';
echo $value;
