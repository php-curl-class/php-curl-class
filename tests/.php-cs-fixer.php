<?php

$finder = PhpCsFixer\Finder::create()
    ->in(dirname(__DIR__));

$config = new PhpCsFixer\Config();
$config
    ->setRules([
        'no_unused_imports' => true,
    ])
    ->setFinder($finder);

return $config;
