<?php

declare(strict_types=1);

namespace App\Ship\Processors;

use App\Ship\Interfaces\Process;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements Process<Model>
 */
readonly class ByIdsProcess implements Process
{
    /**
     * @param array<int, int|string>|null $ids
     */
    public function __construct(
        private ?array $ids,
    ) {
    }

    public function apply(Builder $query): Builder
    {
        if (!$this->ids) {
            return $query;
        }

        return $query->whereKey($this->ids);
    }
}
