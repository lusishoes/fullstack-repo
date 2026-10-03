<?php

declare(strict_types=1);

namespace App\Modules\Locations\Dto;

use App\Ship\Parents\Dto;
use Spatie\LaravelData\Attributes\Validation\Min;

class SearchLocationsDto extends Dto
{
    public function __construct(
        #[Min(2)]
        public string $q,
    ) {
    }
}
