# Troubleshooting

### Ensure you have the latest version of the library installed

```bash
$ cd php-curl-class/
$ composer update
$ composer info
```
Compare your version with latest release ![](https://img.shields.io/github/release/php-curl-class/php-curl-class.svg?style=flat-square&sort=semver&color=rgba(0,0,0,0)&label=) which is also listed on the [releases page](https://github.com/php-curl-class/php-curl-class/releases).

### Ensure php is using the latest version of curl

```bash
$ php -r 'var_dump(curl_version());'
```

Compare your version of curl with latest release ![](https://img.shields.io/github/v/release/curl/curl.svg?style=flat-square&color=rgba(0,0,0,0)&label=) which is also listed on [curl's releases page](https://github.com/curl/curl/releases).

### Turn on error reporting

```php
error_reporting(E_ALL);
```

### Print some information that may hint at the cause of failure

```php
error_reporting(E_ALL);
$curl = new Curl();
$curl->get('https://www.example.com/');
echo 'error: ' . $curl->error . "\n";
echo 'errorCode: ' . $curl->errorCode . "\n";
echo 'errorMessage: ' . $curl->errorMessage . "\n";
echo 'curlError: ' . $curlError . "\n";
echo 'curlErrorCode: ' . $curlErrorCode . "\n";
echo 'curlErrorMessage: ' . $curlErrorMessage . "\n";
echo 'httpError: ' . $httpError . "\n";
echo 'httpStatusCode: ' . $httpStatusCode . "\n";
echo 'httpErrorMessage: ' . $httpErrorMessage . "\n";
echo 'requestHeaders:' . "\n";
var_dump($curl->requestHeaders);
echo 'responseHeaders:' . "\n";
var_dump($curl->responseHeaders);
```

### Turn on verbose mode

```php
error_reporting(E_ALL);
$curl = new Curl();
$curl->verbose();
$curl->get('https://www.example.com/');
var_dump($curl);
```

### Compare request with and without the library

```php
error_reporting(E_ALL);
$curl = new Curl();
$curl->get('https://www.example.com/');
var_dump($curl);
```

```php
error_reporting(E_ALL);
$ch = curl_init();
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'https://www.example.com/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$raw_response = curl_exec($ch);
$curl_error_code = curl_errno($ch);
$curl_error_message = curl_error($ch);
$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$request_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
var_dump($http_status_code);
var_dump($curl_error_code);
var_dump($curl_error_message);
var_dump($request_headers);
var_dump($raw_response);
```

### Ensure you have the latest version of composer installed

```bash
$ composer self-update
$ composer --version
```

Compare your version of composer with latest release ![](https://img.shields.io/github/v/release/composer/composer.svg?style=flat-square&color=rgba(0,0,0,0)&label=) which is also listed on [composer's releases page](https://github.com/composer/composer/releases).
