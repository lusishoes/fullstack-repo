<?php

declare(strict_types=1);

namespace App\Modules\Health\Controllers;

use App\Modules\Health\Actions\GetHealthAction;
use App\Modules\Health\Resources\HealthResource;
use App\Ship\Parents\Controller;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Состояние сервиса и его зависимостей: базы и кэша.
     */
    public function __invoke(GetHealthAction $action): JsonResponse
    {
        $health = $action->run();

        if (!$health->isHealthy()) {
            return $this->success(
                HealthResource::make($health),
                message: 'Часть сервисов недоступна',
                status: 503,
            );
        }

        return $this->success(HealthResource::make($health));
    }
}
