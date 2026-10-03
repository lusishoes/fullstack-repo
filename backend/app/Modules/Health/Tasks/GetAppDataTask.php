<?php

declare(strict_types=1);

namespace App\Modules\Health\Tasks;

use App\Ship\Parents\Task;

final class GetAppDataTask extends Task
{
    public function run(): string
    {
        return config('app.name');
    }
}
