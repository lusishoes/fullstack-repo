<?php

declare(strict_types=1);

namespace App\Ship\Loaders;

use App\Ship\Facades\Ship;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

trait ProvidersLoaderTrait
{
    /**
     * Loads only the Main Service Providers from the Containers.
     * All the Service Providers (registered inside the main), will be
     * loaded from the `boot()` function on the parent of the Main
     * Service Providers.
     */
    public function loadProvidersFromModules(string $modulesPath): void
    {
        $containerProvidersDirectory = $modulesPath . '/Providers';
        $this->loadProviders($containerProvidersDirectory);
    }

    public function loadServiceProviders(): void
    {
        /** @phpstan-ignore-next-line nullCoalesce.property */
        foreach ($this->serviceProviders ?? [] as $provider) {
            if (class_exists($provider)) {
                $this->loadProvider($provider);
            }
        }
    }

    private function loadProviders(string $directory): void
    {
        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $file) {
                $serviceProviderClass = Ship::getClassFullNameFromFile($file->getPathname());
                $this->loadProvider($serviceProviderClass);
            }
        }
    }

    private function loadProvider(string $providerFullName): void
    {
        App::register($providerFullName);
    }
}
