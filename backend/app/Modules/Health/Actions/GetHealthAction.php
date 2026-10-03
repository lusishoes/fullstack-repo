<?php

declare(strict_types=1);

namespace App\Modules\Health\Actions;

use App\Modules\Health\Dto\HealthDto;
use App\Modules\Health\Tasks\CheckCacheTask;
use App\Modules\Health\Tasks\CheckDatabaseTask;
use App\Modules\Health\Tasks\GetAppDataTask;
use App\Ship\Parents\Action;

class GetHealthAction extends Action
{
    public function __construct(
        private readonly CheckDatabaseTask $checkDatabaseTask,
        private readonly CheckCacheTask $checkCacheTask,
        private readonly GetAppDataTask $getAppDataTask,
    ) {
    }

    public function run(): HealthDto
    {
        return new HealthDto(
            database: $this->checkDatabaseTask->run(),
            cache: $this->checkCacheTask->run(),
            app: $this->getAppDataTask->run(),
        );
    }
}
