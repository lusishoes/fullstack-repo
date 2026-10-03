<?php

declare(strict_types=1);

namespace App\Ship\Exceptions;

use App\Ship\Enums\ErrorCode;
use Exception;
use Throwable;

class AbstractBaseException extends Exception
{
    protected ErrorCode $errorCode = ErrorCode::FATAL_ERROR;

    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        /**
         * Дополнительные мета-данные для ответа.
         */
        protected array $meta = [],
        ?Throwable $previous = null,
    ) {
        $message = $message ?: $this->message;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getErrorCode(): ErrorCode
    {
        return $this->errorCode;
    }
}
