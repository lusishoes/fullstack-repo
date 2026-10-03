<?php

declare(strict_types=1);

namespace App\Ship\Interfaces;

use Closure;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface Processable
{
    /**
     * Применяет набор критериев к запросу
     *
     * @param Process<TModel>|class-string<Process<TModel>>|array<array-key, Process<TModel>|class-string<Process<TModel>>> $classes - массив классов процессоров, которые реализуют интерфейс Process
     * @param array<array-key, mixed> $properties - если в конструктор процессора есть параметры, указывать их здесь (имя параметра === ключ массива)
     */
    public function throughProcessors(
        Process|string|array $classes,
        array $properties = [],
        bool $reset = false,
    ): static;

    /**
     * Применяет набор критериев к запросу, если выполнено условие
     *
     * @param Closure|bool $value - условие выполнения
     * @param Process<TModel>|class-string<Process<TModel>>|array<array-key, Process<TModel>|class-string<Process<TModel>>> $classes - массив классов процессоров, которые реализуют интерфейс Process
     * @param array<array-key, mixed> $properties - если в конструктор процессора есть параметры, указывать их здесь (имя параметра === ключ массива)
     */
    public function throughProcessorsThen(
        Closure|bool $value,
        Process|string|array $classes,
        array $properties = [],
    ): static;
}
