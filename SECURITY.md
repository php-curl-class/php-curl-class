### Security

* Don't blindly accept arbitrary urls. Curl supports many protocols including `FILE`. The following would show the contents of `file:///etc/passwd`.

```php
// https://www.example.com/fetch_page.php?url=file%3A%2F%2F%2Fetc%2Fpasswd
$unsafe_url = $_GET['url']; // DANGER!
$curl = new Curl();
$curl->get($unsafe_url);
echo $curl->response;
```
