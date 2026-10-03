<?php

declare(strict_types=1);

use App\Ship\Exceptions\Handlers\ApiExceptionRenderer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware()
    ->withExceptions(static function (Exceptions $exceptions): void {
        $exceptions->render(
            static fn(Throwable $e, Request $request) => app(ApiExceptionRenderer::class)->render($e, $request),
        );
    })->create();
