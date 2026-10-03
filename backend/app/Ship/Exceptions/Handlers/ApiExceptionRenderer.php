<?php

declare(strict_types=1);

namespace App\Ship\Exceptions\Handlers;

use App\Ship\Enums\ErrorCode;
use App\Ship\Exceptions\AbstractApiException;
use App\Ship\Exceptions\AbstractValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Отдаёт ошибку API в едином конверте `{ success: false, message, code?, errors? }`.
 */
final class ApiExceptionRenderer
{
    public function render(Throwable $e, Request $request): ?JsonResponse
    {
        if (!$request->is('api/*', 'admin/*') && !$request->expectsJson()) {
            return null;
        }

        return match (true) {
            $e instanceof AbstractApiException => $this->json(
                e: $e,
                message: $e->getMessage(),
                status: $e->getStatusCode(),
                errorCode: $e->getErrorCode(),
                extra: $e->getMeta(),
            ),
            $e instanceof AbstractValidationException || $e instanceof ValidationException => $this->json(
                e: $e,
                message: __('validation.failed'),
                status: 422,
                extra: ['errors' => $e->errors()],
            ),
            $e instanceof AuthenticationException => $this->json(
                e: $e,
                message: 'Необходимо войти в систему',
                status: 401,
            ),
            $e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException => $this->json(
                e: $e,
                message: 'Недостаточно прав',
                status: 403,
            ),
            $e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException => $this->json(
                e: $e,
                message: 'Упс, не удалось найти',
                status: 404,
                errorCode: ErrorCode::NOT_FOUND,
            ),
            $e instanceof HttpExceptionInterface => $this->json(
                e: $e,
                message: 'Ошибка запроса',
                status: $e->getStatusCode(),
            ),
            default => $this->json(
                e: $e,
                message: config('app.debug') ? $e->getMessage() : 'Внутренняя ошибка сервера',
                status: 500,
                errorCode: ErrorCode::FATAL_ERROR,
            ),
        };
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function json(
        Throwable $e,
        string $message,
        int $status,
        ?ErrorCode $errorCode = null,
        array $extra = [],
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
            ...$extra,
            ...($errorCode !== null ? ['code' => $errorCode->value] : []),
            ...(config('app.debug') ? ['debug' => [
                'exception' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]] : []),
        ];

        return response()->json(
            $payload,
            $status,
            options: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }
}
