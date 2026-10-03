<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Коллекция API ресурсов с поддержкой дополнительного payload через withData.
 *
 * @template TContext of array<string, mixed>
 */
class ApiResourceCollection extends AnonymousResourceCollection
{
    /**
     * @param TContext $payload
     */
    public function withData(array $payload): static
    {
        $this->collection->each(
            static fn(AbstractApiResource $resource): AbstractApiResource => $resource->withData($payload),
        );

        return $this;
    }
}
