<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use App\Ship\Loaders\AliasesLoaderTrait;
use App\Ship\Loaders\ProvidersLoaderTrait;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Override;

abstract class ServiceProvider extends BaseServiceProvider
{
    use ProvidersLoaderTrait;
    use AliasesLoaderTrait;

    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
    }

    /**
     * Register anything in the container.
     */
    #[Override]
    public function register(): void
    {
        $this->loadServiceProviders();
        $this->loadAliases();
    }
}
