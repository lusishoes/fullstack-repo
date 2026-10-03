<?php

declare(strict_types=1);

namespace App\Ship\Interfaces;

interface PaginatableData
{
    public function page(): int;

    public function perPage(): int;
}
