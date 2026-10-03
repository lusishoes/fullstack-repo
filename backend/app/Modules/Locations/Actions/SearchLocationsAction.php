<?php

declare(strict_types=1);

namespace App\Modules\Locations\Actions;

use App\Modules\Locations\Dto\LocationDto;
use App\Modules\Locations\Dto\SearchLocationsDto;
use App\Ship\Parents\Action;

class SearchLocationsAction extends Action
{
    public function __construct(
        // TODO 4: внедрить OpenMeteoGeocodingClient (как таски в GetHealthAction)
    ) {
    }

    /**
     * @return list<LocationDto>
     */
    public function run(SearchLocationsDto $dto): array
    {
        // TODO 5: вызвать поиск у клиента с q из DTO и вернуть результат
    }
}
