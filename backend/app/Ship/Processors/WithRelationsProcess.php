<?php

declare(strict_types=1);

namespace App\Ship\Processors;

use App\Ship\Interfaces\Process;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 *
 * @implements Process<TModel>
 */
final readonly class WithRelationsProcess implements Process
{
    /**
     * @param array<string, mixed>|list<string>|string $relations
     */
    public function __construct(
        private array|string $relations,
    ) {
    }

    public function apply(Builder $query): Builder
    {
        return $query->with($this->relations);
    }
}
