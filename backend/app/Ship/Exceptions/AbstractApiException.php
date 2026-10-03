<?php

declare(strict_types=1);

namespace App\Ship\Exceptions;

abstract class AbstractApiException extends AbstractBaseException
{
    protected int $statusCode = 400;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
