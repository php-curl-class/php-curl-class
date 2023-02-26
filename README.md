# PHP Curl Class: HTTP requests made easy

[![](https://img.shields.io/github/release/php-curl-class/php-curl-class.svg?style=flat-square&sort=semver)](https://github.com/php-curl-class/php-curl-class/releases/)
[![](https://img.shields.io/github/license/php-curl-class/php-curl-class.svg?style=flat-square)](https://github.com/php-curl-class/php-curl-class/blob/master/LICENSE)
[![](https://img.shields.io/github/actions/workflow/status/php-curl-class/php-curl-class/ci.yml?style=flat-square&label=build&branch=master)](https://github.com/php-curl-class/php-curl-class/actions/workflows/ci.yml)
[![](https://img.shields.io/github/actions/workflow/status/php-curl-class/php-curl-class/release.yml?style=flat-square&label=release&branch=master)](https://github.com/php-curl-class/php-curl-class/releases/)
[![](https://img.shields.io/packagist/dt/php-curl-class/php-curl-class.svg?style=flat-square)](https://github.com/php-curl-class/php-curl-class/releases/)

PHP Curl Class makes it easy to send HTTP requests and integrate with web APIs.

![PHP Curl Class screencast](www/img/screencast.gif)

---

- [Installation](#installation)
- [Requirements](#requirements)
- [Quick Start and Examples](#quick-start-and-examples)
- [Available Methods](#available-methods)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Testing](#testing)
- [Contributing](#contributing)

---

### Installation

To install PHP Curl Class, run the following command:

    composer require php-curl-class/php-curl-class

To install the latest commit version:

    composer require php-curl-class/php-curl-class @dev

Installation instructions to use the `composer` command can be found on https://github.com/composer/composer.

### Requirements

PHP Curl Class works with PHP 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, and 8.2.

### Quick Start and Examples

More examples are available under [/examples](https://github.com/php-curl-class/php-curl-class/tree/master/examples).

```php
require __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->get('https://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
    $curl->diagnose();
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}
```

```php
// https://www.example.com/search?q=keyword
$curl = new Curl();
$curl->get('https://www.example.com/search', [
    'q' => 'keyword',
]);
```

```php
$curl = new Curl();
$curl->post('https://www.example.com/login/', [
    'username' => 'myusername',
    'password' => 'mypassword',
]);
```

```php
$curl = new Curl();
$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('MyUserAgent/0.0.1 (+https://www.example.com/bot.html)');
$curl->setReferrer('https://www.example.com/url?url=https%3A%2F%2Fwww.example.com%2F');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setCookie('key', 'value');
$curl->get('https://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->errorMessage . "\n";
} else {
    echo 'Response:' . "\n";
    var_dump($curl->response);
}

var_dump($curl->requestHeaders);
var_dump($curl->responseHeaders);
```

```php
$curl = new Curl();
$curl->setFollowLocation();
$curl->get('https://shortn.example.com/bHbVsP');
```

```php
$curl = new Curl();
$curl->put('https://api.example.com/user/', [
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
]);
```

```php
$curl = new Curl();
$curl->patch('https://api.example.com/profile/', [
    'image' => '@path/to/file.jpg',
]);
```

```php
$curl = new Curl();
$curl->patch('https://api.example.com/profile/', [
    'image' => new CURLFile('path/to/file.jpg'),
]);
```

```php
$curl = new Curl();
$curl->delete('https://api.example.com/user/', [
    'id' => '1234',
]);
```

```php
// Enable all supported encoding types and download a file.
$curl = new Curl();
$curl->setOpt(CURLOPT_ENCODING , '');
$curl->download('https://www.example.com/file.bin', '/tmp/myfile.bin');
```

```php
// Case-insensitive access to headers.
$curl = new Curl();
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
echo $curl->responseHeaders['Content-Type'] . "\n"; // image/png
echo $curl->responseHeaders['CoNTeNT-TyPE'] . "\n"; // image/png
```

```php
// Manual clean up.
$curl->close();
```

```php
// Example access to curl object.
curl_set_opt($curl->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
curl_close($curl->curl);
```

```php
require __DIR__ . '/vendor/autoload.php';

use Curl\MultiCurl;

// Requests in parallel with callback functions.
$multi_curl = new MultiCurl();

$multi_curl->success(function($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
    echo 'response:' . "\n";
    var_dump($instance->response);
});
$multi_curl->error(function($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});
$multi_curl->complete(function($instance) {
    echo 'call completed' . "\n";
});

$multi_curl->addGet('https://www.google.com/search', [
    'q' => 'hello world',
]);
$multi_curl->addGet('https://duckduckgo.com/', [
    'q' => 'hello world',
]);
$multi_curl->addGet('https://www.bing.com/search', [
    'q' => 'hello world',
]);

$multi_curl->start(); // Blocks until all items in the queue have been processed.
```

More examples are available under [/examples](https://github.com/php-curl-class/php-curl-class/tree/master/examples).

### Available Methods
```php
Curl::__construct($base_url = null, $options = [])
Curl::__destruct()
Curl::__get($name)
Curl::_fastDownload($url, $filename, $connections = 4)
Curl::attemptRetry()
Curl::beforeSend($callback)
Curl::buildPostData($data)
Curl::call()
Curl::close()
Curl::complete($callback)
Curl::delete($url, $query_parameters = [], $data = [])
Curl::diagnose($return = false)
Curl::disableTimeout()
Curl::download($url, $mixed_filename)
Curl::error($callback)
Curl::exec($ch = null)
Curl::execDone()
Curl::get($url, $data = [])
Curl::getAttempts()
Curl::getBeforeSendCallback()
Curl::getCompleteCallback()
Curl::getCookie($key)
Curl::getCurl()
Curl::getCurlErrorCode()
Curl::getCurlErrorMessage()
Curl::getDownloadCompleteCallback()
Curl::getDownloadFileName()
Curl::getErrorCallback()
Curl::getErrorCode()
Curl::getErrorMessage()
Curl::getFileHandle()
Curl::getHttpErrorMessage()
Curl::getHttpStatusCode()
Curl::getId()
Curl::getInfo($opt = null)
Curl::getJsonDecoder()
Curl::getOpt($option)
Curl::getRawResponse()
Curl::getRawResponseHeaders()
Curl::getRemainingRetries()
Curl::getRequestHeaders()
Curl::getResponse()
Curl::getResponseCookie($key)
Curl::getResponseCookies()
Curl::getResponseHeaders()
Curl::getRetries()
Curl::getRetryDecider()
Curl::getSuccessCallback()
Curl::getUrl()
Curl::getXmlDecoder()
Curl::head($url, $data = [])
Curl::isChildOfMultiCurl()
Curl::isCurlError()
Curl::isError()
Curl::isHttpError()
Curl::options($url, $data = [])
Curl::patch($url, $data = [])
Curl::post($url, $data = '', $follow_303_with_post = false)
Curl::progress($callback)
Curl::put($url, $data = [])
Curl::removeHeader($key)
Curl::reset()
Curl::search($url, $data = [])
Curl::setAutoReferer($auto_referer = true)
Curl::setAutoReferrer($auto_referrer = true)
Curl::setBasicAuthentication($username, $password = '')
Curl::setConnectTimeout($seconds)
Curl::setCookie($key, $value)
Curl::setCookieFile($cookie_file)
Curl::setCookieJar($cookie_jar)
Curl::setCookieString($string)
Curl::setCookies($cookies)
Curl::setDefaultDecoder($mixed = 'json')
Curl::setDefaultHeaderOut()
Curl::setDefaultJsonDecoder()
Curl::setDefaultTimeout()
Curl::setDefaultUserAgent()
Curl::setDefaultXmlDecoder()
Curl::setDigestAuthentication($username, $password = '')
Curl::setFile($file)
Curl::setFollowLocation($follow_location = true)
Curl::setForbidReuse($forbid_reuse = true)
Curl::setHeader($key, $value)
Curl::setHeaders($headers)
Curl::setInterface($interface)
Curl::setJsonDecoder($mixed)
Curl::setMaxFilesize($bytes)
Curl::setMaximumRedirects($maximum_redirects)
Curl::setOpt($option, $value)
Curl::setOpts($options)
Curl::setPort($port)
Curl::setProtocols($protocols)
Curl::setProxy($proxy, $port = null, $username = null, $password = null)
Curl::setProxyAuth($auth)
Curl::setProxyTunnel($tunnel = true)
Curl::setProxyType($type)
Curl::setRange($range)
Curl::setRedirectProtocols($redirect_protocols)
Curl::setReferer($referer)
Curl::setReferrer($referrer)
Curl::setRetry($mixed)
Curl::setStop($callback = null)
Curl::setTimeout($seconds)
Curl::setUrl($url, $mixed_data = '')
Curl::setUserAgent($user_agent)
Curl::setXmlDecoder($mixed)
Curl::stop()
Curl::success($callback)
Curl::unsetHeader($key)
Curl::unsetProxy()
Curl::verbose($on = true, $output = 'STDERR')
MultiCurl::__construct($base_url = null)
MultiCurl::__destruct()
MultiCurl::addCurl(Curl $curl)
MultiCurl::addDelete($url, $query_parameters = [], $data = [])
MultiCurl::addDownload($url, $mixed_filename)
MultiCurl::addGet($url, $data = [])
MultiCurl::addHead($url, $data = [])
MultiCurl::addOptions($url, $data = [])
MultiCurl::addPatch($url, $data = [])
MultiCurl::addPost($url, $data = '', $follow_303_with_post = false)
MultiCurl::addPut($url, $data = [])
MultiCurl::addSearch($url, $data = [])
MultiCurl::beforeSend($callback)
MultiCurl::close()
MultiCurl::complete($callback)
MultiCurl::disableTimeout()
MultiCurl::error($callback)
MultiCurl::getOpt($option)
MultiCurl::removeHeader($key)
MultiCurl::setAutoReferer($auto_referer = true)
MultiCurl::setAutoReferrer($auto_referrer = true)
MultiCurl::setBasicAuthentication($username, $password = '')
MultiCurl::setConcurrency($concurrency)
MultiCurl::setConnectTimeout($seconds)
MultiCurl::setCookie($key, $value)
MultiCurl::setCookieFile($cookie_file)
MultiCurl::setCookieJar($cookie_jar)
MultiCurl::setCookieString($string)
MultiCurl::setCookies($cookies)
MultiCurl::setDigestAuthentication($username, $password = '')
MultiCurl::setFile($file)
MultiCurl::setFollowLocation($follow_location = true)
MultiCurl::setForbidReuse($forbid_reuse = true)
MultiCurl::setHeader($key, $value)
MultiCurl::setHeaders($headers)
MultiCurl::setInterface($interface)
MultiCurl::setJsonDecoder($mixed)
MultiCurl::setMaximumRedirects($maximum_redirects)
MultiCurl::setOpt($option, $value)
MultiCurl::setOpts($options)
MultiCurl::setPort($port)
MultiCurl::setProxies($proxies)
MultiCurl::setProxy($proxy, $port = null, $username = null, $password = null)
MultiCurl::setProxyAuth($auth)
MultiCurl::setProxyTunnel($tunnel = true)
MultiCurl::setProxyType($type)
MultiCurl::setRange($range)
MultiCurl::setRateLimit($rate_limit)
MultiCurl::setReferer($referer)
MultiCurl::setReferrer($referrer)
MultiCurl::setRequestTimeAccuracy()
MultiCurl::setRetry($mixed)
MultiCurl::setTimeout($seconds)
MultiCurl::setUrl($url, $mixed_data = '')
MultiCurl::setUserAgent($user_agent)
MultiCurl::setXmlDecoder($mixed)
MultiCurl::start()
MultiCurl::stop()
MultiCurl::success($callback)
MultiCurl::unsetHeader($key)
MultiCurl::unsetProxy()
MultiCurl::verbose($on = true, $output = 'STDERR')
```

### Security

See [SECURITY](https://github.com/php-curl-class/php-curl-class/blob/master/SECURITY.md) for security considerations.

### Troubleshooting

See [TROUBLESHOOTING](https://github.com/php-curl-class/php-curl-class/blob/master/TROUBLESHOOTING.md) for help troubleshooting.

### Testing

See [TESTING](https://github.com/php-curl-class/php-curl-class/blob/master/TESTING.md) for testing information.

### Contributing

1. Check for open issues or open a new issue to start a discussion around a bug or feature.
1. Fork the repository on GitHub to start making your changes.
1. Write one or more tests for the new feature or that expose the bug.
1. Make code changes to implement the feature or fix the bug.
1. Send a pull request to get your changes merged and published.
