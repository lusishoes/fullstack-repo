<?php

declare(strict_types=1);

namespace App\Modules\Locations\Dto;

use App\Ship\Parents\Dto;

/**
 * Один найденный населённый пункт из Open-Meteo Geocoding.
 */
class LocationDto extends Dto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $country,
        public float $latitude,
        public float $longitude,
    ) {
    }
}
