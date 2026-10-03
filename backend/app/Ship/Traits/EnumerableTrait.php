<?php

declare(strict_types=1);

namespace App\Ship\Traits;

use UnitEnum;

/**
 * @phpstan-ignore-next-line trait.unused
 *
 * @mixin UnitEnum
 */
trait EnumerableTrait
{
    /**
     * @param list<self>|null $list
     *
     * @return list<non-falsy-string>
     */
    public static function names(?array $list = null): array
    {
        return array_column($list ?? static::cases(), 'name');
    }

    /**
     * @param list<self>|null $list
     *
     * @return list<mixed>
     */
    public static function values(?array $list = null): array
    {
        return array_column($list ?? static::cases(), 'value');
    }

    /**
     * @return array<non-falsy-string, mixed>
     */
    public static function array(): array
    {
        return array_combine(static::names(), static::values());
    }

    /**
     * @param list<self> $enums
     */
    private function isOneOfArray(array $enums): bool
    {
        return in_array($this, $enums, true);
    }
}
