<?php
require 'Helper.php';

use \Helper\Test;

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
} elseif ($request_method === 'PUT') {
    if (strpos($content_type, 'application/x-www-form-urlencoded') === 0) {
        parse_str($http_raw_post_data, $_PUT);
        $data_values = $_PUT;
    }
} elseif ($request_method === 'PATCH') {
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
} elseif ($test === 'get') {
    echo http_build_query($_GET);
    exit;
} elseif ($test === 'post') {
    echo http_build_query($_POST);
    exit;
} elseif ($test === 'put') {
    echo $http_raw_post_data;
    exit;
} elseif ($test === 'post_multidimensional') {
    echo $http_raw_post_data;
    exit;
} elseif ($test === 'post_file_path_upload') {
    echo mime_content_type($_FILES[$key]['tmp_name']);
    exit;
} elseif ($test === 'put_file_handle') {
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, $http_raw_post_data);
    echo mime_content_type($tmp_filename);
    unlink($tmp_filename);
    exit;
} elseif ($test === 'request_method') {
    header('X-REQUEST-METHOD: ' . $request_method);
    echo $request_method;
    exit;
} elseif ($test === 'request_uri') {
    echo $_SERVER['REQUEST_URI'];
    exit;
} elseif ($test === 'cookiejar') {
    setcookie('mycookie', 'yum');
    exit;
} elseif ($test === 'multiple_cookie') {
    setcookie('cookie1', 'scrumptious');
    setcookie('cookie2', 'mouthwatering');
    exit;
} elseif ($test === 'response_header') {
    header('Content-Type: application/json');
    header('ETag: ' . md5('worldpeace'));
    exit;
} elseif ($test === 'response_body') {
    echo 'OK';
    exit;
} elseif ($test === 'json_response') {
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
} elseif ($test === 'xml_response') {
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
} elseif ($test === 'upload_response') {
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    move_uploaded_file($_FILES['image']['tmp_name'], $tmp_filename);
    header('Content-Type: application/json');
    header('ETag: ' . md5_file($tmp_filename));
    echo json_encode(array(
        'file_path' => $tmp_filename,
    ));
    exit;
} elseif ($test === 'upload_cleanup') {
    $unsafe_file_path = $_POST['file_path'];
    echo var_export(unlink($unsafe_file_path), true);
    exit;
} elseif ($test === 'download_response') {
    $unsafe_file_path = $_GET['file_path'];
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="image.png"');
    header('Content-Length: ' . filesize($unsafe_file_path));
    header('ETag: ' . md5_file($unsafe_file_path));
    readfile($unsafe_file_path);
    exit;
} elseif ($test === 'error_message') {
    if (function_exists('http_response_code')) {
        http_response_code(401);
    } else {
        header('HTTP/1.1 401 Unauthorized');
    }
    exit;
} elseif ($test === 'redirect') {
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
