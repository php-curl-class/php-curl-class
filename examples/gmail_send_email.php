<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const CLIENT_ID = 'XXXXXXXXXXXX.apps.googleusercontent.com';
const CLIENT_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXX';

session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange the authorization code for an access token.
    $curl = new Curl();
    $curl->post('https://accounts.google.com/o/oauth2/token', [
        'code' => $code,
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'redirect_uri' => implode('', [
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
            '://',
            $_SERVER['SERVER_NAME'],
            $_SERVER['SCRIPT_NAME'],
        ]),
        'grant_type' => 'authorization_code',
    ]);

    if ($curl->error) {
        echo $curl->response->error . ': ' . $curl->response->error_description;
        exit;
    }

    $_SESSION['access_token'] = $curl->response->access_token;
    header('Location: ?');
    exit;
} elseif (!empty($_SESSION['access_token'])) {
    // Use the access token to send an email.
    $curl = new Curl();
    $curl->setHeader('Content-Type', 'message/rfc822');
    $curl->setHeader('Authorization', 'OAuth ' . $_SESSION['access_token']);

    $boundary = md5(time());
    $raw =
        'MIME-Version: 1.0' . "\r\n" .
        'Subject: hi' . "\r\n" .
        'To: John Doe <jdoe@example.com>' . "\r\n" .
        'Content-Type: multipart/alternative; boundary=' . $boundary . "\r\n" .
        "\r\n" .
        '--' . $boundary . "\r\n" .
        'Content-Type: text/plain; charset=UTF-8' . "\r\n" .
        "\r\n" .
        'hello, world' . "\r\n" .
        "\r\n" .
        '--' . $boundary . "\r\n" .
        'Content-Type: text/html; charset=UTF-8' . "\r\n" .
        "\r\n" .
        '<em>hello, world</em>' . "\r\n" .
        '';

    $curl->post('https://www.googleapis.com/upload/gmail/v1/users/me/messages/send', $raw);

    echo 'Email ' . $curl->response->id . ' was sent.';
} else {
    $curl = new Curl();
    $curl->get('https://accounts.google.com/o/oauth2/auth', [
        'scope' => 'https://www.googleapis.com/auth/gmail.compose',
        'redirect_uri' => implode('', [
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
            '://',
            $_SERVER['SERVER_NAME'],
            $_SERVER['SCRIPT_NAME'],
        ]),
        'response_type' => 'code',
        'client_id' => CLIENT_ID,
        'approval_prompt' => 'force',
    ]);

    $url = $curl->responseHeaders['Location'];
    echo '<a href="' . $url . '">Continue</a>';
}
