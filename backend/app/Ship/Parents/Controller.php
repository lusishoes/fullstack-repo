<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @param array<array-key, mixed>|JsonResource|AnonymousResourceCollection|null $data
     */
    public function success(
        array|JsonResource|AnonymousResourceCollection|null $data = [],
        ?string $message = '',
        int $status = 200,
    ): JsonResponse {
        $resource = is_array($data) || $data === null
            ? new JsonResource($data)
            : $data;

        return $resource
            ->additional([
                ...$resource->additional,
                'message' => $message,
                'success' => true,
            ])
            ->response()
            ->setStatusCode($status);
    }

    public function failed(?string $message = '', int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
