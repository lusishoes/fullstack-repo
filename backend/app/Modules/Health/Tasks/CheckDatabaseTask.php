<?php

declare(strict_types=1);

namespace App\Modules\Health\Tasks;

use App\Ship\Parents\Task;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CheckDatabaseTask extends Task
{
    public function run(): bool
    {
        try {
            DB::connection()->select('select 1');

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
