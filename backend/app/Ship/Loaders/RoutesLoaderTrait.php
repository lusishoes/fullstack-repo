<?php

declare(strict_types=1);

namespace App\Ship\Loaders;

use App\Ship\Facades\Ship;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

trait RoutesLoaderTrait
{
    public function runRoutesAutoLoader(): void
    {
        foreach (Ship::getModulesPaths() as $modulePath) {
            $this->loadModuleRoutes($modulePath);
        }

        foreach (Ship::getAdminPaths() as $adminPath) {
            $this->loadAdminModuleRoutes($adminPath);
        }
    }

    /**
     * Зарегистрировать маршруты для одного модуля.
     */
    protected function loadModuleRoutes(string $modulePath): void
    {
        $routesDir = $modulePath . DIRECTORY_SEPARATOR . 'Routes';

        if (!File::isDirectory($routesDir)) {
            return;
        }

        $apiFile = $routesDir . DIRECTORY_SEPARATOR . 'api.php';
        $webFile = $routesDir . DIRECTORY_SEPARATOR . 'web.php';

        if (File::exists($apiFile)) {
            $this->loadApiRoutes($modulePath, $apiFile);
        }

        if (File::exists($webFile)) {
            $this->loadWebRoutes($modulePath, $webFile);
        }
    }

    protected function loadAdminModuleRoutes(string $adminPath): void
    {
        $apiFile = $adminPath . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'api.php';

        if (File::exists($apiFile)) {
            $this->loadAdminRoutes($adminPath, $apiFile);
        }
    }

    /**
     * Зарегистрировать API маршруты.
     */
    protected function loadApiRoutes(string $modulePath, string $apiFile): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->getModuleNamespace($modulePath))
            ->group(static function () use ($apiFile): void {
                require $apiFile;
            });
    }

    /**
     * Зарегистрировать API маршруты административной панели.
     */
    protected function loadAdminRoutes(string $modulePath, string $apiFile): void
    {
        Route::prefix('admin')
            ->middleware(['api', 'auth:sanctum'])
            ->namespace($this->getModuleNamespace($modulePath))
            ->group(static function () use ($apiFile): void {
                require $apiFile;
            });
    }

    /**
     * Зарегистрировать WEB маршруты.
     */
    protected function loadWebRoutes(string $modulePath, string $webFile): void
    {
        Route::middleware('web')
            ->namespace($this->getModuleNamespace($modulePath))
            ->group(static function () use ($webFile): void {
                require $webFile;
            });
    }

    /**
     * Получить namespace модуля для контроллеров.
     */
    protected function getModuleNamespace(string $modulePath): ?string
    {
        $controllersPath = $modulePath . DIRECTORY_SEPARATOR . 'Controllers';

        if (!File::isDirectory($controllersPath)) {
            return null;
        }

        $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR, '', $controllersPath);
        $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

        return 'App\\' . $namespace;
    }
}
