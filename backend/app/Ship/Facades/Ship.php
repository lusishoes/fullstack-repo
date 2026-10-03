<?php

declare(strict_types=1);

namespace App\Ship\Facades;

use App\Ship\Foundation\Ship as ShipFoundation;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getClassFullNameFromFile(string $filePathName)
 * @method static list<string> getModulesPaths()
 * @method static list<string> getAdminPaths()
 *
 * @see ShipFoundation
 */
class Ship extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Ship';
    }
}
