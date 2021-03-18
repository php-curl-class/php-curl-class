<?php
$server_start = microtime(true);

// Prevent direct access unless testing.
if (getenv('PHP_CURL_CLASS_TEST_MODE_ENABLED') !== 'yes' &&
    @$_SERVER['PHP_CURL_CLASS_TEST_MODE_ENABLED'] !== 'yes') {
    exit;
}

require_once 'ContentRangeServer.php';
require_once 'RangeHeader.php';
require_once 'Helper.php';

use Helper\Test;

$http_raw_post_data = file_get_contents('php://input');
$_PUT = [];
$_PATCH = [];
$_DELETE = [];

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
} elseif ($request_method === 'DELETE') {
    if (strpos($content_type, 'application/x-www-form-urlencoded') === 0) {
        parse_str($http_raw_post_data, $_DELETE);
        $data_values = $_DELETE;
    }
}

$test = '';
if (isset($_SERVER['HTTP_X_DEBUG_TEST'])) {
    $test = $_SERVER['HTTP_X_DEBUG_TEST'];
} elseif (isset($_GET['test'])) {
    $test = $_GET['test'];
}
$key = isset($data_values['key']) ? $data_values['key'] : '';

if ($test === 'http_basic_auth') {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.1 401 Unauthorized');
        echo 'canceled';
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'username' => $_SERVER['PHP_AUTH_USER'],
        'password' => $_SERVER['PHP_AUTH_PW'],
    ]);
    exit;
} elseif ($test === 'http_digest_auth') {
    $users = [
        'myusername' => 'mypassword',
    ];

    $realm = 'Restricted area';
    $qop = 'auth';
    $nonce = md5(uniqid());
    $opaque = md5(uniqid());
    if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
        header('HTTP/1.1 401 Unauthorized');
        header(sprintf(
            'WWW-Authenticate: Digest realm="%s", qop="%s", nonce="%s", opaque="%s"',
            $realm,
            $qop,
            $nonce,
            $opaque
        ));
        echo 'canceled';
        exit;
    }

    $data = [
        'nonce' => '',
        'nc' => '',
        'cnonce' => '',
        'qop' => '',
        'username' => '',
        'uri' => '',
        'response' => '',
    ];
    preg_match_all(
        '@(' . implode('|', array_keys($data)) . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@',
        $_SERVER['PHP_AUTH_DIGEST'],
        $matches,
        PREG_SET_ORDER
    );
    foreach ($matches as $match) {
        $data[$match['1']] = $match['3'] ? $match['3'] : $match['4'];
    }

    $A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
    $A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
    $valid_response = md5(
        $A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2
    );

    if ($data['response'] !== $valid_response) {
        header('HTTP/1.1 401 Unauthorized');
        echo 'invalid';
        exit;
    }

    echo 'valid';
    exit;
} elseif ($test === 'get') {
    echo http_build_query($_GET);
    exit;
} elseif ($test === 'post') {
    echo http_build_query($_POST);
    exit;
} elseif ($test === 'post_json') {
    echo $http_raw_post_data;
    exit;
} elseif ($test === 'put') {
    echo $http_raw_post_data;
    exit;
} elseif ($test === 'patch') {
    echo $http_raw_post_data;
    exit;
} elseif ($test === 'search') {
    echo $http_raw_post_data;
    exit;
} elseif ($test === 'post_multidimensional' || $test === 'post_multidimensional_with_file') {
    header('Content-Type: application/json');
    echo json_encode([
        'post' => $_POST,
        'files' => $_FILES,
    ]);
    exit;
} elseif ($test === 'post_file_path_upload') {
    echo Helper\mime_type($_FILES[$key]['tmp_name']);
    exit;
} elseif ($test === 'put_file_handle') {
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, $http_raw_post_data);
    echo Helper\mime_type($tmp_filename);
    unlink($tmp_filename);
    exit;
} elseif ($test === 'request_method') {
    header('X-REQUEST-METHOD: ' . $request_method);
    echo $request_method;
    exit;
} elseif ($test === 'request_uri') {
    echo $_SERVER['REQUEST_URI'];
    exit;
} elseif ($test === 'setcookie') {
    foreach ($_COOKIE as $key => $value) {
        setcookie($key, $value);
    }
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
    if ($request_method === 'POST') {
        $key = $_POST['key'];
        $value = $_POST['value'];
        header($key . ': ' . $value);
    } else {
        header('Content-Type: application/json');
    }
    echo json_encode([
        'null' => null,
        'true' => true,
        'false' => false,
        'integer' => 1,
        'float' => 3.14,
        'empty' => '',
        'string' => 'string',
    ]);
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
} elseif ($test === 'xml_with_cdata_response') {
    header('Content-Type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>
<rss>
    <items>
        <item>
            <id>1</id>
            <ref>33ee7e1eb504b6619c1b445ca1442c21</ref>
            <title><![CDATA[The Title]]></title>
            <description><![CDATA[The description.]]></description>
            <link><![CDATA[https://www.example.com/page.html?foo=bar&baz=wibble#hash]]></link>
        </item>
        <item>
            <id>2</id>
            <ref>b5c0b187fe309af0f4d35982fd961d7e</ref>
            <title><![CDATA[Another Title]]></title>
            <description><![CDATA[Some description.]]></description>
            <link><![CDATA[https://www.example.org/image.png?w=1265.73&h=782.26]]></link>
        </item>
    </items>
</rss>';
    exit;
} elseif ($test === 'upload_response') {
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    move_uploaded_file($_FILES['image']['tmp_name'], $tmp_filename);
    header('Content-Type: application/json');
    header('ETag: ' . md5_file($tmp_filename));
    echo json_encode([
        'file_path' => $tmp_filename,
    ]);
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
} elseif ($test === 'download_file_size') {
    $bytes = isset($_GET['bytes']) ? $_GET['bytes'] : 1234;
    $str = str_repeat('.', $bytes);
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . strlen($str));
    header('ETag: ' . md5($str));
    echo $str;
    exit;
} elseif ($test === 'download_file_range') {
    $unsafe_file_path = $_GET['file_path'];
    $server = new ContentRangeServer\ContentRangeServer();
    $server->serve($unsafe_file_path);
    exit;
} elseif ($test === 'timeout') {
    // Use --no-buffer to view loading indicator (e.g.
    // curl --header "X-DEBUG-TEST: timeout" --include --no-buffer 127.0.0.1:8000/?seconds=3).
    header('Content-Type: application/json');
    $unsafe_seconds = (int)$_GET['seconds'];
    $start = microtime(true);
    echo '{' . "\n";
    echo '  "loading": "';

    $dots_printed = 0;
    while (true) {
        usleep(1000000 / 100);

        $elapsed = microtime(true) - $start;
        $dots_to_print = floor($elapsed) - $dots_printed;

        if ($dots_to_print) {
            echo str_repeat('.', $dots_to_print);
            $dots_printed += $dots_to_print;
        }

        if ($elapsed >= $unsafe_seconds) {
            break;
        }
    }

    echo '",' . "\n";
    echo '  "elapsed_seconds": "' . $elapsed . '",' . "\n";
    echo '  "server_port": "' . ((int)$_SERVER['SERVER_PORT']) . '",' . "\n";
    echo '  "server_start": "' . $server_start . '",' . "\n";
    echo '  "server_stop": "' . microtime(true) . '"' . "\n";
    echo '}' . "\n";
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
} elseif ($test === 'delete_with_body') {
    header('Content-Type: application/json');
    echo json_encode([
        'get' => $_GET,
        'delete' => $_DELETE,
    ]);
    exit;
} elseif ($test === 'data_values') {
    header('Content-Type: application/json');
    echo json_encode($data_values);
    exit;
} elseif ($test === 'post_redirect_get') {
    if (isset($_GET['redirect'])) {
        echo 'Redirected: ' . $request_method;
    } else {
        if ($request_method === 'POST') {
            if (function_exists('http_response_code')) {
                http_response_code(303);
            } else {
                header('HTTP/1.1 303 See Other');
            }

            header('Location: ?redirect');
        } else {
            echo 'Request method is ' . $request_method . ', but POST was expected';
        }
    }

    exit;
} elseif ($test === 'retry') {
    session_start();

    if (isset($_SESSION['failures_remaining'])) {
        $failures_remaining = $_SESSION['failures_remaining'];
    } else {
        $failures_remaining = (int)$_GET['failures'];
        $_SESSION['failures_remaining'] = $failures_remaining;
    }

    if ($failures_remaining >= 1) {
        $_SESSION['failures_remaining'] -= 1;

        header('HTTP/1.1 503 Service Unavailable');
        echo 'Service Unavailable';
        echo ' (remaining failures: ' . $_SESSION['failures_remaining'] . ')';
        exit;
    }

    header('HTTP/1.1 202 Accepted');
    echo '202 Accepted';
    echo ' (remaining failures: ' . $_SESSION['failures_remaining'] . ')';
    exit;
} elseif ($test === '404') {
    header('HTTP/1.1 404 Not Found');
    echo '404 Not Found';
    exit;
}

header('Content-Type: text/plain');

$data_mapping = [
    'cookie' => $_COOKIE,
    'delete' => $_GET,
    'get' => $_GET,
    'patch' => $_PATCH,
    'post' => $_POST,
    'put' => $_PUT,
    'server' => $_SERVER,
];

if (!empty($test)) {
    $data = $data_mapping[$test];
    if (empty($key)) {
        // Return all values when a key is not specified.
        $value = http_build_query($data);
    } else {
        // Return individual value when a key is specified.
        $value = isset($data[$key]) ? $data[$key] : '';
    }
    echo $value;
}
