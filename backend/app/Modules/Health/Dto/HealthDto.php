<?php

declare(strict_types=1);

namespace App\Modules\Health\Dto;

use App\Ship\Parents\Dto;

class HealthDto extends Dto
{
    public function __construct(
        public bool $database,
        public bool $cache,
        public string $app,
    ) {
    }

    public function isHealthy(): bool
    {
        return $this->database && $this->cache;
    }
}
