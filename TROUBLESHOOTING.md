# Troubleshooting

### Turn on verbose mode

```php
$curl = new Curl();
$curl->verbose();
```

### Compare request with and without the library

```php
$curl = new Curl();
$curl->get('https://www.example.com/');
var_dump($curl);
```

```php
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
```
