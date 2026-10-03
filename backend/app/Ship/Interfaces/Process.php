<?php

declare(strict_types=1);

namespace App\Ship\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface Process
{
    /**
     * @param Builder<TModel> $query
     *
     * @return Builder<TModel>
     */
    public function apply(Builder $query): Builder;
}
