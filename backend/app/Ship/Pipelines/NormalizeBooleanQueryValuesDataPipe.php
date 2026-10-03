<?php

declare(strict_types=1);

namespace App\Ship\Pipelines;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\DataPipes\DataPipe;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataClass;
use Spatie\LaravelData\Support\DataProperty;

final readonly class NormalizeBooleanQueryValuesDataPipe implements DataPipe
{
    /**
     * @param array<array-key, mixed> $properties
     * @param CreationContext<BaseData<object, mixed, array-key>> $creationContext
     *
     * @return array<array-key, mixed>
     */
    public function handle(
        mixed $payload,
        DataClass $class,
        array $properties,
        CreationContext $creationContext,
    ): array {
        if (!$payload instanceof Request) {
            return $properties;
        }

        $query = $payload->query->all();
        $body = $payload->isJson()
            ? $payload->json()->all()
            : $payload->request->all();

        $class->properties->each(
            static function (DataProperty $property) use ($body, &$properties, $query): void {
                if (!$property->type->type->acceptsType('bool')) {
                    return;
                }

                $inputName = $property->inputMappedName ?? $property->name;

                if (Arr::has($body, $inputName)) {
                    return;
                }

                if (!Arr::has($query, $inputName)) {
                    return;
                }

                $value = Arr::get($query, $inputName);
                $normalizedValue = match (is_string($value) ? Str::lower($value) : $value) {
                    'true' => true,
                    'false' => false,
                    default => $value,
                };

                Arr::set($properties, $inputName, $normalizedValue);
            },
        );

        return $properties;
    }
}
