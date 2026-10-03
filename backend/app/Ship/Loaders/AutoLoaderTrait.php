<?php

declare(strict_types=1);

namespace App\Ship\Loaders;

use App\Ship\Facades\Ship;

trait AutoLoaderTrait
{
    use MigrationsLoaderTrait;
    use ProvidersLoaderTrait;
    use CommandsLoaderTrait;

    public function runLoadersBoot(): void
    {
        $this->loadCommandsFromShip();

        foreach (Ship::getModulesPaths() as $modulesPath) {
            $this->loadMigrationsFromModules($modulesPath);
            $this->loadCommandsFromModules($modulesPath);
        }

        foreach (Ship::getAdminPaths() as $adminPath) {
            $this->loadCommandsFromModules($adminPath);
        }
    }

    public function runLoaderRegister(): void
    {
        foreach (Ship::getModulesPaths() as $modulesPath) {
            $this->loadProvidersFromModules($modulesPath);
        }

        foreach (Ship::getAdminPaths() as $adminPath) {
            $this->loadProvidersFromModules($adminPath);
        }
    }
}
