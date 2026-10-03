<?php

declare(strict_types=1);

namespace App\Ship\Providers;

use App\Ship\Parents\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::preventLazyLoading(!$this->app->isProduction());
    }
}
