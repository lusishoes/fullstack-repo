<?php

declare(strict_types=1);

namespace App\Ship\Processors;

use App\Ship\Interfaces\PaginatableData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template TModel of Model
 */
final readonly class PaginateProcess
{
    public function __construct(
        private PaginatableData $data,
    ) {
    }

    /**
     * @param Builder<TModel> $query
     *
     * @return LengthAwarePaginator<int, TModel>
     */
    public function apply(Builder $query): LengthAwarePaginator
    {
        return $query->paginate($this->data->perPage(), page: $this->data->page());
    }
}
