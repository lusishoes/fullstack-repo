<?php

declare(strict_types=1);

namespace App\Modules\Health\Resources;

use App\Modules\Health\Dto\HealthDto;
use App\Ship\Parents\AbstractApiResource;
use Override;

/**
 * @extends AbstractApiResource<HealthDto, array<string, mixed>>
 *
 * @mixin HealthDto
 */
class HealthResource extends AbstractApiResource
{
    /**
     * @return array{
     *     status: string,
     *     app: string,
     *     services: array{database: bool, cache: bool}
     * }
     */
    #[Override]
    public function toArray($request): array
    {
        return [
            'status' => $this->resource->isHealthy() ? 'ok' : 'degraded',
            'app' => $this->resource->app,
            'services' => [
                'database' => $this->resource->database,
                'cache' => $this->resource->cache,
            ],
        ];
    }
}
