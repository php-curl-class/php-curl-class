<?php

$finder = PhpCsFixer\Finder::create()
    ->in(dirname(__DIR__));

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'constant_case' => [
            'case' => 'lower',
        ],
        'elseif' => true,
        'encoding' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'access',
                'author',
            ],
            'case_sensitive' => false,
        ],
        'is_null' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_leading_import_slash' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => false,
        ],
        'phpdoc_align' => [
            'align' => 'vertical',
        ],
        'phpdoc_indent' => true,
        'phpdoc_line_span' => [
            'const' => 'multi',
            'method' => 'multi',
            'property' => 'multi',
        ],
        'phpdoc_order' => [
            'order' => [
                'param',
                'return',
                'throws',
                'see',
            ],
        ],
        'phpdoc_param_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => [
            'groups' => [
                [
                    'deprecated',
                    'expectedException',
                    'param',
                    'requires',
                    'return',
                    'see',
                    'throws',
                ],
            ],
            'skip_unlisted_annotations' => false,
        ],
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'alpha',
        ],
        'short_scalar_cast' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'trailing_comma_in_multiline' => true,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder);

return $config;
