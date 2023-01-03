<?php

declare(strict_types = 1);

/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
    // # Issue statistics:
    // PhanUndeclaredMethod : 10+ occurrences
    // PhanTypeMismatchArgumentNullableInternal : 7 occurrences
    // PhanCommentDuplicateParam : 3 occurrences
    // PhanDeprecatedFunction : 3 occurrences
    // PhanTypeMismatchArgumentInternalProbablyReal : 2 occurrences
    // PhanTypeMismatchDeclaredParamNullable : 2 occurrences
    // PhanTypeMismatchPropertyProbablyReal : 2 occurrences
    // PhanAccessMethodInternal : 1 occurrence
    // PhanTypeMismatchArgumentNullable : 1 occurrence
    // PhanTypeMismatchArgumentProbablyReal : 1 occurrence
    // PhanTypePossiblyInvalidDimOffset : 1 occurrence
    // PhanTypeSuspiciousNonTraversableForeach : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Controller/SearchController.php' => ['PhanAccessMethodInternal'],
        'src/Controller/TranslateController.php' => ['PhanTypeMismatchArgumentNullableInternal', 'PhanTypeMismatchDeclaredParamNullable', 'PhanTypePossiblyInvalidDimOffset'],
        'src/Model/Svg/SvgFile.php' => ['PhanTypeMismatchArgumentInternalProbablyReal', 'PhanTypeMismatchArgumentNullableInternal', 'PhanTypeMismatchPropertyProbablyReal', 'PhanUndeclaredMethod'],
        'src/OOUI/TranslationsFieldset.php' => ['PhanCommentDuplicateParam', 'PhanTypeSuspiciousNonTraversableForeach'],
        'src/Service/MediaWikiApi.php' => ['PhanDeprecatedFunction'],
        'src/Service/Renderer.php' => ['PhanTypeMismatchDeclaredParamNullable'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
