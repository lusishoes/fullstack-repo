<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->group('feature')
    ->in(
        'Feature',
        '../app/Modules/*/Tests/Feature',
        '../app/Admin/*/Tests/Feature',
        '../app/Services/*/*/Tests/Feature',
    );

pest()
    ->extend(TestCase::class)
    ->group('unit')
    ->in(
        'Unit',
        '../app/Modules/*/Tests/Unit',
        '../app/Admin/*/Tests/Unit',
        '../app/Services/*/*/Tests/Unit',
    );
