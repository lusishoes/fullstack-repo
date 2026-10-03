<?php

declare(strict_types=1);

namespace App\Modules\Locations\Resources;

use App\Modules\Locations\Dto\LocationDto;
use App\Ship\Parents\AbstractApiResource;
use Override;

/**
 * @extends AbstractApiResource<LocationDto, array<string, mixed>>
 *
 * @mixin LocationDto
 */
class LocationResource extends AbstractApiResource
{
    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     country: string|null,
     *     latitude: float,
     *     longitude: float
     * }
     */
    #[Override]
    public function toArray($request): array
    {
        // TODO 6: вернуть массив с ключами id, name, country, latitude, longitude
        //         (по образцу HealthResource: значения берутся из $this->resource)
    }
}
