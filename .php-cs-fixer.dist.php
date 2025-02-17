<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR12' => true, // Apply PSR-12 coding standard
        'ordered_imports' => [
            'sort_algorithm' => 'alpha', // Sort imports alphabetically
        ],
    ])
    ->setFinder(PhpCsFixer\Finder::create()->in(__DIR__)); // Set your source directory
