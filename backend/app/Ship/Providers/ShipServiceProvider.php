<?php

declare(strict_types=1);

namespace App\Ship\Providers;

use App\Ship\Foundation\Ship;
use App\Ship\Loaders\AutoLoaderTrait;
use App\Ship\Parents\ServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Override;

class ShipServiceProvider extends ServiceProvider
{
    use AutoLoaderTrait;

    /**
     * @var array<int, class-string>
     */
    public array $serviceProviders = [
        AppServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * @var array<string, class-string>
     */
    protected array $aliases = [
        'Ship' => Ship::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->runLoadersBoot();
    }

    #[Override]
    public function register(): void
    {
        $this->app->singleton('Ship', Ship::class);

        parent::register();

        if (class_exists(IdeHelperServiceProvider::class)) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->runLoaderRegister();
    }
}
