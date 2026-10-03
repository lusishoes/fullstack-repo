<?php

declare(strict_types=1);

namespace App\Ship\Loaders;

use Illuminate\Support\Facades\File;

trait MigrationsLoaderTrait
{
    public function loadMigrationsFromModules(string $modulePath): void
    {
        $containerMigrationDirectory = $modulePath . '/Database/Migrations';
        $this->loadMigrations($containerMigrationDirectory);
    }

    private function loadMigrations(string $directory): void
    {
        if (File::isDirectory($directory)) {
            $this->loadMigrationsFrom($directory);
        }
    }
}
