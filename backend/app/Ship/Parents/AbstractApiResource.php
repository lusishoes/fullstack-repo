<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * Базовый API ресурс.
 *
 * @template TModel of object
 * @template TContext of array<string, mixed>
 *
 * @mixin TModel
 *
 * @property-read TModel $resource
 *
 * @method static ApiResourceCollection<TContext> collection(mixed $resource)
 */
abstract class AbstractApiResource extends JsonResource
{
    /**
     * Создает ресурс только если данные подготовлены.
     *
     * @param TModel|null $resource
     */
    public static function makeOrNull(?object $resource): ?static
    {
        return $resource === null ? null : static::make($resource);
    }

    /**
     * Пробрасывает данные в ресурс так, чтобы к ним можно было обращаться
     * через $this->key, но они НЕ добавляются автоматически в итоговый JSON.
     *
     * Пример:
     *
     *      BasketResource::make($basket)->withData(['refreshed' => true]);
     *
     * внутри ресурса:
     *
     *      $this->refreshed
     *
     * @param TContext $payload
     */
    public function withData(array $payload): static
    {
        foreach ($payload as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @return ApiResourceCollection<TContext>
     */
    #[Override]
    protected static function newCollection($resource): ApiResourceCollection
    {
        /**
         * @var ApiResourceCollection<TContext> $collection
         */
        $collection = new ApiResourceCollection($resource, static::class);

        return $collection;
    }
}
