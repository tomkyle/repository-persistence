<?php
$header = <<<EOF
tomkyle/repository-persistence

Scaffold for Repository-and-Persistence design pattern
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
    'header_comment' => [
        'comment_type' => 'PHPDoc',
        'header' => $header,
        'location' => 'after_open',
        'separate' => 'both',
    ]
])->setFinder($finder);
