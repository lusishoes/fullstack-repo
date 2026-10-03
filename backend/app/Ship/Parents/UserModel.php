<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use App\Ship\Traits\FactoryLocatorTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin Eloquent
 */
abstract class UserModel extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    /** @use FactoryLocatorTrait<static> */
    use FactoryLocatorTrait {
        FactoryLocatorTrait::newFactory insteadof HasFactory;
    }
}
