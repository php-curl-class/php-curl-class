# Security Considerations

### Url may point to system files

* Don't blindly accept urls from users as they may point to system files. Curl supports many protocols including `FILE`.
  The following would show the contents of `file:///etc/passwd`.

```bash
# Attacker.
$ curl https://www.example.com/display_webpage.php?url=file%3A%2F%2F%2Fetc%2Fpasswd
```

```php
// display_webpage.php
$url = $_GET['url']; // DANGER!
$curl = new Curl();
$curl->get($url);
echo $curl->response;
```

Safer:

```php
function is_allowed_url($url, $allowed_url_schemes = ['http', 'https']) {
    $valid_url = filter_var($url, FILTER_VALIDATE_URL) !== false;
    if ($valid_url) {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        return in_array($scheme, $allowed_url_schemes, true);
    }
    $valid_ip = filter_var($url, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    return $valid_ip;
}

$url = $_GET['url'];
if (!is_allowed_url($url)) {
    die('Unsafe url detected.');
}

$curl = new Curl();
$curl->setProtocols(CURLPROTO_HTTPS);
$curl->setRedirectProtocols(CURLPROTO_HTTPS);
$curl->get($url);
```

### Url may point to internal urls

* Url may point to internal urls including those behind a firewall (e.g. http://192.168.0.1/ or ftp://192.168.0.1/). Use
  a whitelist to allow certain urls rather than a blacklist.

* Use `Curl::setProtocols()` and `Curl::setRedirectProtocols()` to restrict allowed protocols.

```php
// Allow only HTTPS protocols.
$curl->setProtocols(CURLPROTO_HTTPS);
$curl->setRedirectProtocols(CURLPROTO_HTTPS);
```

```php
// Allow HTTPS and HTTP protocols.
$curl->setProtocols(CURLPROTO_HTTPS | CURLPROTO_HTTP);
$curl->setRedirectProtocols(CURLPROTO_HTTPS | CURLPROTO_HTTP);
```

### Request data may refer to system files

* Request data prefixed with the `@` character may have special interpretation and read from system files.

```bash
# Attacker.
$ curl https://www.example.com/upload_photo.php --data "photo=@/etc/passwd"
```

```php
// upload_photo.php
$curl = new Curl();
$curl->post('http://www.anotherwebsite.com/', [
    'photo' => $_POST['photo'], // DANGER!
]);
```

### Unsafe response with redirection enabled

* Requests with redirection enabled may return responses from unexpected sources.
  Downloading https://www.example.com/image.png may redirect and download https://www.evil.com/virus.exe

```php
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true); // DANGER!
$curl->download('https://www.example.com/image.png', 'my_image.png');
```

```php
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true); // DANGER!
$curl->get('https://www.example.com/image.png');
```

### Keep SSL protections enabled

* Do not disable SSL protections.

```php
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // DANGER!
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // DANGER!
```

### Prevent XML External Entity injection

* Set the following when using the default PHP XML parser to prevent XML external entity injection.

```php
libxml_disable_entity_loader(true);
```

### Prevent PHP execution of library files

PHP files in this library are not intended to be accessible by users browsing websites. Prevent direct access to library files by moving the library folder at least one level higher than the web root directory. Alternatively, configure the server to disable php file execution for all library files.

#### For WordPress plugin developers

WordPress plugin developers that wish to incorporate the PHP Curl Class library into their plugin, should take special care to include only the "core" library files.

Do one of the following:

Option 1. Download an official release from the [releases page](https://github.com/php-curl-class/php-curl-class/releases) and incorporate the files contained in the compressed file into the plugin. The releases include only the necessary php files for the library to function.

Option 2. Manually copy only the [src/](https://github.com/php-curl-class/php-curl-class/tree/master/src) directory into your plugin. Be sure not to copy any other php files as they may be executable by users visiting the php files directly.
