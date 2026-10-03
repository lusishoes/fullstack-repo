<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use App\Ship\Interfaces\PaginatableData;
use App\Ship\Interfaces\Processable;
use App\Ship\Processors\PaginateProcess;
use App\Ship\Traits\HasProcessors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template TModel of EloquentModel
 *
 * @implements Processable<TModel>
 */
abstract class Repository implements Processable
{
    /** @use HasProcessors<TModel> */
    use HasProcessors;

    /** @var TModel */
    protected EloquentModel $model;

    public function __construct()
    {
        $this->model = $this->setModel();
        $this->processors = collect();
    }

    /**
     * @return Builder<TModel>
     */
    protected function getQuery(): Builder
    {
        $query = $this->newQuery();

        if (!$this->processors->isEmpty()) {
            return $this->applyProcessors($query);
        }

        return $query;
    }

    /**
     * @return Builder<TModel>
     */
    protected function newQuery(): Builder
    {
        /** @var Builder<TModel> $query */
        $query = $this->model::query();

        return $query;
    }

    /**
     * @return LengthAwarePaginator<int, TModel>
     */
    protected function paginateQuery(PaginatableData $data): LengthAwarePaginator
    {
        /** @var PaginateProcess<TModel> $process */
        $process = new PaginateProcess($data);

        return $process->apply($this->getQuery());
    }

    /**
     * @return TModel
     */
    abstract protected function setModel(): EloquentModel;
}
