<?php

declare(strict_types=1);

namespace App\Ship\Factories;

use App\Ship\Interfaces\Process;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

final class ProcessorsFactory
{
    /**
     * @template TModel of Model
     *
     * @param Process<TModel>|class-string<Process<TModel>>|array<array-key, Process<TModel>|class-string<Process<TModel>>> $classes
     * @param array<array-key, mixed> $properties
     *
     * @return array<int, Process<TModel>>
     *
     * @throws ReflectionException
     */
    public static function make(Process|string|array $classes, array $properties): array
    {
        return array_map(
            static fn(Process|string $class): Process => self::instantiate($class, $properties),
            Arr::wrap($classes),
        );
    }

    /**
     * @template TModel of Model
     *
     * @param Process<TModel>|class-string<Process<TModel>> $class
     * @param array<string, mixed> $properties
     *
     * @return Process<TModel>
     *
     * @throws ReflectionException
     */
    private static function instantiate(Process|string $class, array $properties): Process
    {
        if ($class instanceof Process) {
            return $class;
        }

        $ref = new ReflectionClass($class);
        $constructor = $ref->getConstructor();

        if (!$constructor || $constructor->getNumberOfParameters() === 0) {
            return new $class();
        }

        $args = [];

        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $properties)) {
                $args[] = $properties[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Missing  value for "%s" of %s::__construct',
                    $name,
                    $class,
                ));
            }
        }

        return $ref->newInstanceArgs($args);
    }
}
