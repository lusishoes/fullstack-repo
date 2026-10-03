<?php

declare(strict_types=1);

namespace App\Modules\Locations\Controllers;

use App\Modules\Locations\Dto\SearchLocationsDto;
use App\Ship\Parents\Controller;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    // TODO 7: вторым параметром принять SearchLocationsAction,
    //         вызвать его и вернуть $this->success(LocationResource::collection(...))
    public function __invoke(SearchLocationsDto $dto): JsonResponse
    {
        return $this->success([
            'q' => $dto->q,
        ]);
    }
}
