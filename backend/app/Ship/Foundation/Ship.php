<?php

declare(strict_types=1);

namespace App\Ship\Foundation;

use Illuminate\Support\Facades\File;

class Ship
{
    private const string MODULES_DIRECTORY_NAME = 'Modules';

    private const string ADMIN_DIRECTORY_NAME = 'Admin';

    /**
     * Get the full name (name \ namespace) of a class from its file path
     * result example: (string) "I\Am\The\Namespace\Of\This\Class"
     */
    public function getClassFullNameFromFile(string $filePathName): string
    {
        return sprintf('%s\%s', $this->getClassNamespaceFromFile($filePathName), $this->getClassNameFromFile($filePathName));
    }

    /**
     * @return list<string>
     */
    public function getModulesPaths(): array
    {
        return $this->getAppDirectoryPaths(self::MODULES_DIRECTORY_NAME);
    }

    /**
     * @return list<string>
     */
    public function getAdminPaths(): array
    {
        return $this->getAppDirectoryPaths(self::ADMIN_DIRECTORY_NAME);
    }

    /**
     * Get the class namespace form file path using token
     */
    protected function getClassNamespaceFromFile(string $filePathName): ?string
    {
        $src = file_get_contents($filePathName);

        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespaceOk = false;

        while ($i < $count) {
            $token = $tokens[$i];

            if (is_array($token) && $token[0] === T_NAMESPACE) {
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespaceOk = true;
                        $namespace = trim($namespace);

                        break;
                    }

                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }

                break;
            }

            $i++;
        }

        if (!$namespaceOk) {
            return null;
        }

        return $namespace;
    }

    /**
     * Get the class name from file path using token
     */
    protected function getClassNameFromFile(string $filePathName): mixed
    {
        $php_code = file_get_contents($filePathName);

        $classes = [];
        $tokens = token_get_all($php_code);
        $count = count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] === T_CLASS
                && $tokens[$i - 1][0] === T_WHITESPACE
                && $tokens[$i][0] === T_STRING
            ) {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes[0];
    }

    /**
     * @return list<string>
     */
    private function getAppDirectoryPaths(string $directory): array
    {
        $path = app_path($directory);

        if (!File::isDirectory($path)) {
            return [];
        }

        return File::directories($path);
    }
}
