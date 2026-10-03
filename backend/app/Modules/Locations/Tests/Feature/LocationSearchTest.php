<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\getJson;

beforeEach(function (): void {
    Http::preventStrayRequests();
});

it('ищет города через Open-Meteo', function (): void {
    Http::fake([
        'geocoding-api.open-meteo.com/*' => Http::response([
            'results' => [
                [
                    'id' => 2950159,
                    'name' => 'Берлин',
                    'latitude' => 52.52437,
                    'longitude' => 13.41053,
                    'country_code' => 'DE',
                    'country' => 'Германия',
                    'timezone' => 'Europe/Berlin',
                ],
            ],
            'generationtime_ms' => 1.0,
        ]),
    ]);

    getJson(route('locations.search', ['q' => 'Berlin']))
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'message' => '',
            'data' => [
                [
                    'id' => 2950159,
                    'name' => 'Берлин',
                    'country' => 'Германия',
                    'latitude' => 52.52437,
                    'longitude' => 13.41053,
                ],
            ],
        ]);

    Http::assertSent(static fn(Request $request): bool => $request['name'] === 'Berlin');
});

it('отдаёт пустой список, если Open-Meteo ничего не нашёл', function (): void {
    Http::fake([
        'geocoding-api.open-meteo.com/*' => Http::response(['generationtime_ms' => 1.0]),
    ]);

    getJson(route('locations.search', ['q' => 'Qwxyzqwxyz']))
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'message' => '',
            'data' => [],
        ]);
});

it('отдаёт 422, если q не передан', function (): void {
    getJson(route('locations.search'))
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors('q');
});

it('отдаёт 422, если q короче двух символов', function (): void {
    getJson(route('locations.search', ['q' => 'B']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('q');
});
