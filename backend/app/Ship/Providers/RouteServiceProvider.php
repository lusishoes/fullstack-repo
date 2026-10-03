<?php

declare(strict_types=1);

namespace App\Ship\Providers;

use App\Ship\Loaders\RoutesLoaderTrait;
use App\Ship\Parents\RouteServiceProvider as ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Override;

class RouteServiceProvider extends ServiceProvider
{
    use RoutesLoaderTrait;

    #[Override]
    public function boot(): void
    {
        RateLimiter::for(
            'api',
            static fn(Request $request): Limit => Limit::perMinute(120)
                ->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip())),
        );
    }

    public function map(): void
    {
        $this->runRoutesAutoLoader();
    }
}
