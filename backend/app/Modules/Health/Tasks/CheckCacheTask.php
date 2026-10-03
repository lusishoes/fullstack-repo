<?php

declare(strict_types=1);

namespace App\Modules\Health\Tasks;

use App\Ship\Parents\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

final class CheckCacheTask extends Task
{
    private const string KEY = 'health:probe';

    public function run(): bool
    {
        $probe = Str::random(16);

        try {
            Cache::put(self::KEY, $probe, 10);

            return Cache::pull(self::KEY) === $probe;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
