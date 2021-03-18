<?php
// Perform a post-redirect-get request (POST data and follow 303 redirections
// using GET requests).
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
$curl->post('https://www.example.com/login/', [
    'username' => 'myusername',
    'password' => 'mypassword',
]);

// POST data and follow 303 redirections by POSTing data again. Please note
// that 303 redirections should not be handled this way.
// https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.4
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
$curl->post('https://www.example.com/login/', [
    'username' => 'myusername',
    'password' => 'mypassword',
], false);

// A POST request performs a post-redirect-get by default. Other request
// methods force an option which conflicts with the post-redirect-get behavior.
// Due to technical limitations of PHP engines <5.5.11, it is not possible to
// reset this option. It is therefore impossible to perform a post-redirect-get
// request using a php-curl-class Curl object that has already been used to
// perform other types of requests. Either use a new php-curl-class Curl object
// or upgrade your PHP engine.
