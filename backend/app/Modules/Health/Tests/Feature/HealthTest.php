<?php

declare(strict_types=1);

use function Pest\Laravel\getJson;

it('отдаёт ok, когда база и кэш доступны', function (): void {
    getJson(route('health.show'))
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'message' => '',
            'data' => [
                'status' => 'ok',
                'app' => config('app.name'),
                'services' => [
                    'database' => true,
                    'cache' => true,
                ],
            ],
        ]);
});

it('отдаёт 503 и degraded, когда кэш недоступен', function (): void {
    config([
        'cache.default' => 'redis',
        'database.redis.cache.host' => '127.0.0.1',
        'database.redis.cache.port' => 1,
    ]);

    getJson(route('health.show'))
        ->assertServiceUnavailable()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Часть сервисов недоступна')
        ->assertJsonPath('data.status', 'degraded')
        ->assertJsonPath('data.services.database', true)
        ->assertJsonPath('data.services.cache', false);
});
