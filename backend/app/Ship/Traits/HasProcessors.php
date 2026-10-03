<?php

declare(strict_types=1);

namespace App\Ship\Traits;

use App\Ship\Factories\ProcessorsFactory;
use App\Ship\Interfaces\Process;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of Model
 */
trait HasProcessors
{
    /**
     * @var Collection<int, Process<TModel>>
     */
    private Collection $processors;

    /**
     * Добавляет шаблон процессоров для работы
     *
     * @param Process<TModel>|class-string<Process<TModel>>|array<array-key, Process<TModel>|class-string<Process<TModel>>> $classes
     * @param array<array-key, mixed> $properties
     */
    public function throughProcessors(
        Process|string|array $classes,
        array $properties = [],
        bool $reset = false,
    ): static {
        $this->setProcessors(
            ProcessorsFactory::make($classes, $properties),
            $reset,
        );

        return $this;
    }

    /**
     * Добавляет шаблон процессоров для работы, если выполнено условие
     *
     * @param Process<TModel>|class-string<Process<TModel>>|array<array-key, Process<TModel>|class-string<Process<TModel>>> $classes
     * @param array<array-key, mixed> $properties
     */
    public function throughProcessorsThen(
        Closure|bool $value,
        Process|string|array $classes,
        array $properties = [],
    ): static {
        $value = $value instanceof Closure ? $value($this) : $value;

        return $value ? $this->throughProcessors($classes, $properties) : $this;
    }

    /**
     * @param Builder<TModel> $query
     *
     * @return Builder<TModel>
     */
    protected function applyProcessors(Builder $query): Builder
    {
        return $this->processors->reduce(
            fn(Builder $query, Process $filter): Builder => $filter->apply($query),
            $query,
        );
    }

    /**
     * Применяет набор процессоров к запросу
     *
     * @param array<array-key, Process<TModel>> $processorsArray
     */
    private function setProcessors(array $processorsArray, bool $reset = false): static
    {
        /** @var Collection<int, Process<TModel>> $processors */
        $processors = collect($processorsArray);

        $this->processors = $reset ? $processors : $this->processors->merge($processors);

        return $this;
    }
}
