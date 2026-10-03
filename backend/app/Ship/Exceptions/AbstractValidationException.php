<?php

declare(strict_types=1);

namespace App\Ship\Exceptions;

abstract class AbstractValidationException extends AbstractBaseException
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct(message: __('validation.failed'));
    }

    /** @return array<string, list<string>> */
    public function errors(): array
    {
        return $this->errors;
    }
}
