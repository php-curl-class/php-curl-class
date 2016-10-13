### Security Considerations

* Url may point to system files. Don't blindly accept arbitrary urls from users. Curl supports many protocols including
  `FILE`. The following would show the contents of `file:///etc/passwd`.

```bash
# Attacker.
$ curl https://www.example.com/display_webpage.php?url=file%3A%2F%2F%2Fetc%2Fpasswd
``

```php
// display_webpage.php
$url = $_GET['url']; // DANGER!
$curl = new Curl();
$curl->get($url);
echo $curl->response;
```

Safer:

```php
function is_website_url($url, $allowed_schemes = array('http', 'https')) {
    $validate_url = !(filter_var($url, FILTER_VALIDATE_URL) === false);
    $scheme = parse_url($url, PHP_URL_SCHEME);
    return $validate_url && in_array($scheme, $allowed_schemes, true);
}

$url = $_GET['url'];
if (!is_website_url($url)) {
    die('Unsafe url detected.');
}
```

* Url may point to internal urls behind firewall (e.g. http://192.168.0.1/ or ftp://192.168.0.1/). Use a whitelist to
  allow certain urls. Definitely don't use a blacklist.

* Request data prefixed with the @ character may have special interpretation and read from system files.

```bash
# Attacker.
$ curl https://www.example.com/upload_photo.php --data "photo=@/etc/password"
``

```php
// upload_photo.php
$curl = new Curl();
$curl->post('http://www.anotherwebsite.com/', array(
    'photo' => $_POST['photo'], // DANGER!
));
```

* Requests with redirection enabled may return responses from unexpected sources.
  Downloading https://www.example.com/image.png may redirect and download https://www.evil.com/virus.exe

```php
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true); // DANGER!
$curl->download('http://www.example.com/image.png', 'my_image.png');
```

* Do not disable SSL protections.

```php
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // DANGER!
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // DANGER!
```
