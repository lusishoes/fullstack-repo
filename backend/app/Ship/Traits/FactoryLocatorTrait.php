<?php

declare(strict_types=1);

namespace App\Ship\Traits;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
trait FactoryLocatorTrait
{
    /**
     * Create a new factory instance for the model.
     *
     * @return Factory<TModel>|null
     */
    protected static function newFactory(): ?Factory
    {
        $separator = '\\';
        $factoriesPath = 'Database' . $separator . 'Factories' . $separator;
        $fullPathSections = explode($separator, static::class);
        $moduleName = $fullPathSections[2];
        $nameSpace = 'App' . $separator . 'Modules' . $separator . $moduleName . $separator . $factoriesPath;
        $className = class_basename(static::class);
        $factoryClass = $nameSpace . $className . 'Factory';

        if (!class_exists($factoryClass)) {
            return null;
        }

        /** @var class-string<Factory<TModel>> $factoryClass */
        return $factoryClass::new();
    }
}
