# Curll
 
لطفا پیشنهادات خود را جهت بهتر شدن پروژه ذکر کنید
---

- [نصب](#installation)
- [احتیاجات](#requirements)
- [نمونه کد](#quick-start-and-examples)
- [متد ها](#available-methods)
 
---

### نصب

برای نصب فقط  از طریق composer
    $ composer require php-curl-class/php-curl-class
 
```php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('http://www.example.com/');
```

```php
$curl = new Curl();
$curl->get('http://www.example.com/search', array(
    'q' => 'keyword',
));
```

```php
$curl = new Curl();
$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));

// Perform a post-redirect-get request (POST data and follow 303 redirections using GET requests).
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);¬
$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));

// POST data and follow 303 redirections by POSTing data again.
// Please note that 303 redirections should not be handled this way:
// https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.4
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);¬
$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
), false);
```

 
```php
$curl = new Curl();
$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('');
$curl->setReferrer('');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setCookie('key', 'value');
$curl->get('http://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
}
else {
    echo $curl->response;
}

var_dump($curl->requestHeaders);
var_dump($curl->responseHeaders);
```

```php
$curl = new Curl();
$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
$curl->get('https://encrypted.example.com/');
```

```php
$curl = new Curl();
$curl->put('http://api.example.com/user/', array(
    'first_name' => 'Zach',
    'last_name' => 'Borboa',
));
```

```php
$curl = new Curl();
$curl->patch('http://api.example.com/profile/', array(
    'image' => '@path/to/file.jpg',
));
```

```php
$curl = new Curl();
$curl->patch('http://api.example.com/profile/', array(
    'image' => new CURLFile('path/to/file.jpg'),
));
```

```php
$curl = new Curl();
$curl->delete('http://api.example.com/user/', array(
    'id' => '1234',
));
```

```php
// Enable gzip compression and download a file.
$curl = new Curl();
$curl->setOpt(CURLOPT_ENCODING , 'gzip');
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
```

```php
// Case-insensitive access to headers.
$curl = new Curl();
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
echo $curl->responseHeaders['Content-Type'] . "\n"; // image/png
echo $curl->responseHeaders['CoNTeNT-TyPE'] . "\n"; // image/png
```

```php
$curl->close();
```

```php
// Example access to curl object.
curl_set_opt($curl->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
curl_close($curl->curl);
```

```php
require __DIR__ . '/vendor/autoload.php';

use \Curl\MultiCurl;

// Requests in parallel with callback functions.
$multi_curl = new MultiCurl();

$multi_curl->success(function($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
    echo 'response: ' . $instance->response . "\n";
});
$multi_curl->error(function($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});
$multi_curl->complete(function($instance) {
    echo 'call completed' . "\n";
});

$multi_curl->addGet('https://www.google.com/search', array(
    'q' => 'hello world',
));
$multi_curl->addGet('https://duckduckgo.com/', array(
    'q' => 'hello world',
));
$multi_curl->addGet('https://www.bing.com/search', array(
    'q' => 'hello world',
));

$multi_curl->start(); // Blocks until all items in the queue have been processed.
```

### متد ها
```php
Curl::__construct($base_url = null)
Curl::__destruct()
Curl::beforeSend($callback)
Curl::buildPostData($data)
Curl::call()
Curl::close()
Curl::complete($callback)
Curl::delete($url, $query_parameters = array(), $data = array())
Curl::download($url, $mixed_filename)
Curl::downloadComplete($fh)
Curl::error($callback)
Curl::exec($ch = null)
Curl::get($url, $data = array())
Curl::getCookie($key)
Curl::getOpt($option)
Curl::getResponseCookie($key)
Curl::getResponseCookies()
Curl::head($url, $data = array())
Curl::headerCallback($ch, $header)
Curl::options($url, $data = array())
Curl::patch($url, $data = array())
Curl::post($url, $data = array(), $post_redirect_get = false)
Curl::progress($callback)
Curl::put($url, $data = array())
Curl::setBasicAuthentication($username, $password = '')
Curl::setConnectTimeout($seconds)
Curl::setCookie($key, $value)
Curl::setCookieFile($cookie_file)
Curl::setCookieJar($cookie_jar)
Curl::setDefaultJsonDecoder()
Curl::setDefaultTimeout()
Curl::setDefaultUserAgent()
Curl::setDefaultXmlDecoder()
Curl::setDigestAuthentication($username, $password = '')
Curl::setHeader($key, $value)
Curl::setJsonDecoder($function)
Curl::setOpt($option, $value)
Curl::setPort($port)
Curl::setReferer($referer)
Curl::setReferrer($referrer)
Curl::setTimeout($seconds)
Curl::setURL($url, $data = array())
Curl::setUserAgent($user_agent)
Curl::setXmlDecoder($function)
Curl::success($callback)
Curl::unsetHeader($key)
Curl::verbose($on = true, $output=STDERR)
Curl::http_build_multi_query($data, $key = null)
Curl::is_array_assoc($array)
Curl::is_array_multidim($array)
MultiCurl::__construct($base_url = null)
MultiCurl::__destruct()
MultiCurl::addDelete($url, $query_parameters = array(), $data = array())
MultiCurl::addDownload($url, $mixed_filename)
MultiCurl::addGet($url, $data = array())
MultiCurl::addHead($url, $data = array())
MultiCurl::addOptions($url, $data = array())
MultiCurl::addPatch($url, $data = array())
MultiCurl::addPost($url, $data = array(), $post_redirect_get = false)
MultiCurl::addPut($url, $data = array())
MultiCurl::beforeSend($callback)
MultiCurl::close()
MultiCurl::complete($callback)
MultiCurl::error($callback)
MultiCurl::getOpt($option)
MultiCurl::setBasicAuthentication($username, $password = '')
MultiCurl::setCookie($key, $value)
MultiCurl::setCookieFile($cookie_file)
MultiCurl::setCookieJar($cookie_jar)
MultiCurl::setDigestAuthentication($username, $password = '')
MultiCurl::setHeader($key, $value)
MultiCurl::setJsonDecoder($function)
MultiCurl::setOpt($option, $value)
MultiCurl::setReferer($referer)
MultiCurl::setReferrer($referrer)
MultiCurl::setTimeout($seconds)
MultiCurl::setURL($url)
MultiCurl::setUserAgent($user_agent)
MultiCurl::setXmlDecoder($function)
MultiCurl::start()
MultiCurl::success($callback)
MultiCurl::unsetHeader($key)
MultiCurl::verbose($on = true)
```

### http://behzad.top
