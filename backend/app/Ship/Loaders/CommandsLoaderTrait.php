<?php

declare(strict_types=1);

namespace App\Ship\Loaders;

use App\Ship\Facades\Ship;
use Illuminate\Support\Facades\File;
use SplFileInfo;

trait CommandsLoaderTrait
{
    public function loadCommandsFromModules(string $modulesPath): void
    {
        $containerCommandsDirectory = $modulesPath . '/Commands';
        $this->loadTheConsoles($containerCommandsDirectory);
    }

    public function loadCommandsFromShip(): void
    {
        $shipCommandsDirectory = base_path('app/Ship/Commands');
        $this->loadTheConsoles($shipCommandsDirectory);
    }

    private function loadTheConsoles(string $directory): void
    {
        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $consoleFile) {
                if (!$this->isRouteFile($consoleFile)) {
                    $consoleClass = Ship::getClassFullNameFromFile($consoleFile->getPathname());
                    $this->commands([$consoleClass]);
                }
            }
        }
    }

    private function isRouteFile(SplFileInfo $consoleFile): bool
    {
        return $consoleFile->getFilename() === 'closures.php';
    }
}
