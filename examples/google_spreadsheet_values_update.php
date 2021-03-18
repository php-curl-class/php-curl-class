<?php
require __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
const OAUTH2_TOKEN_URI = 'https://www.googleapis.com/oauth2/v4/token';

const CLIENT_ID = 'XXXXXXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.apps.googleusercontent.com';
const CLIENT_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXX';
const REDIRECT_URI = 'https://www.example.com/oauth2callback';

if (php_sapi_name() !== 'cli') {
    throw new Exception('This application must be run on the command line.');
}

// Request authorization from the user.
$auth_url = OAUTH2_AUTH_URL . '?' . http_build_query([
    'access_type' => 'offline',
    'approval_prompt' => 'force',
    'client_id' => CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'https://www.googleapis.com/auth/spreadsheets',
]);
echo 'Open the following link in your browser:' . "\n";
echo $auth_url . "\n";
echo 'Enter verification code: ';
$code = trim(fgets(STDIN));

// Exchange authorization code for an access token.
$curl = new Curl();
$curl->post(OAUTH2_TOKEN_URI, [
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => REDIRECT_URI,
]);
$access_token = $curl->response;

// Update spreadsheet.
$spreadsheet_id = '1Z2cXhdG-K44KgSzHTcGhx1dY-xY31yuYGwX21F4GeUp';
$range = 'Sheet1!A1';
$url = 'https://sheets.googleapis.com/v4/spreadsheets/' . $spreadsheet_id . '/values/' . $range;
$url .= '?' . http_build_query([
    'valueInputOption' => 'USER_ENTERED',
]);

$data = [
    'values' => [
        [
            'This is cell A1',
            'B1',
            'C1',
            'and D1',
        ],
    ],
];

$curl = new Curl();
$curl->setHeader('Content-Type', 'application/json');
$curl->setHeader('Authorization', 'Bearer ' . $access_token->access_token);
$curl->put($url, $data);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
    var_dump($curl);
} else {
    var_dump($curl->response);
}
