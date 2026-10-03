<?php

declare(strict_types=1);

namespace App\Ship\Enums;

use App\Ship\Traits\EnumerableTrait;

enum ErrorCode: string
{
    use EnumerableTrait;

    /* Непредвиденная критическая ошибка */
    case FATAL_ERROR = 'fatal_error';

    /* Ошибка внешнего сервиса */
    case EXTERNAL_SERVICE_ERROR = 'external_service_error';

    /* Сущность не найдена */
    case NOT_FOUND = 'not_found';
}
