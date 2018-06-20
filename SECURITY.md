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
function is_allowed_url($url, $allowed_url_schemes = array('http', 'https')) {
    $valid_url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED) !== false;
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
```

### Url may point to internal urls

* Url may point to internal urls including those behind a firewall (e.g. http://192.168.0.1/ or ftp://192.168.0.1/). Use
  a whitelist to allow certain urls rather than a blacklist.

### Request data may refer to system files

* Request data prefixed with the `@` character may have special interpretation and read from system files.

```bash
# Attacker.
$ curl https://www.example.com/upload_photo.php --data "photo=@/etc/passwd"
```

```php
// upload_photo.php
$curl = new Curl();
$curl->post('http://www.anotherwebsite.com/', array(
    'photo' => $_POST['photo'], // DANGER!
));
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
