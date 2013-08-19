<?php
header('Content-Type: text/plain');

$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

$var = '_' . strtoupper($request_method);
$var = $$var;

$test = isset($var['test']) ? '_' . strtoupper($var['test']) : '';
$test = $$test;
//var_dump('test:', $test);

$key = isset($var['key']) ? $var['key'] : '';
//var_dump('key:', $key);

$value = isset($test[$key]) ? $test[$key] : '';
//var_dump('value:', $value);

echo $value;
