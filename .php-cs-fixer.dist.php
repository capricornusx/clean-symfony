<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@PSR12' => true,
            'phpdoc_align' => true,
            'phpdoc_separation' => true,
            'no_unused_imports' => true,
            'function_declaration' => ['closure_function_spacing' => 'none'],
        ]
    )
    ->setFinder($finder);
