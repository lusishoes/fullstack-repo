<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withCache(__DIR__ . '/storage/rector')
    ->withSkip([
        CatchExceptionNameMatchingTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        BinaryOpNullableToInstanceofRector::class,
    ]);
