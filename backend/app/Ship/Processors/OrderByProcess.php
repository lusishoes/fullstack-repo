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
final readonly class OrderByProcess implements Process
{
    /**
     * @param string|array<int|string, string> $columns
     */
    public function __construct(
        private string|array $columns,
    ) {
    }

    public function apply(Builder $query): Builder
    {
        $columns = is_string($this->columns) ? [$this->columns] : $this->columns;

        foreach ($columns as $column => $direction) {
            if (is_int($column)) {
                $query->orderBy($direction);

                continue;
            }

            $query->orderBy($column, $direction);
        }

        return $query;
    }
}
