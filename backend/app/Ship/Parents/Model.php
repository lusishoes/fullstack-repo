<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use App\Ship\Traits\FactoryLocatorTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @mixin Eloquent
 */
abstract class Model extends BaseModel
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    /** @use FactoryLocatorTrait<static> */
    use FactoryLocatorTrait {
        FactoryLocatorTrait::newFactory insteadof HasFactory;
    }
}
