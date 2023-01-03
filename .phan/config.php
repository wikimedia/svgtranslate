<?php

declare(strict_types = 1);

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command-line arguments will be applied
 * after this file is read.
 */
return [
    'target_php_version' => '7.4',

    'directory_list' => [
        'src',
        'vendor',
    ],

    'exclude_file_regex' => '@^vendor/.*/(tests?|Tests?)/@',

    'exclude_analysis_directory_list' => [
        'vendor/',
    ],

    'suppress_issue_types' => [
        // Done by PHPCS, which can also read inline @var comment
        'PhanUnreferencedUseNormal',
    ],

    'plugins' => [
        'PregRegexCheckerPlugin',
        'UnusedSuppressionPlugin',
        'DuplicateExpressionPlugin',
        'LoopVariableReusePlugin',
        'RedundantAssignmentPlugin',
        'UnreachableCodePlugin',
        'SimplifyExpressionPlugin',
        'DuplicateArrayKeyPlugin',
        'UseReturnValuePlugin',
        'AddNeverReturnTypePlugin',
    ],
];
