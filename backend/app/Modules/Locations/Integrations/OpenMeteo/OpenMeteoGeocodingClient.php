<?php

declare(strict_types=1);

namespace App\Modules\Locations\Integrations\OpenMeteo;

use App\Modules\Locations\Dto\LocationDto;

/**
 * Клиент Open-Meteo Geocoding API: https://open-meteo.com/en/docs/geocoding-api
 */
class OpenMeteoGeocodingClient
{
    /**
     * @return list<LocationDto>
     */
    public function search(string $name): array
    {
        // TODO 1: GET-запрос через фасад Http на config('services.open_meteo.geocoding_url')
        //         с query-параметрами: name, count = 10, language = 'ru', format = 'json'.
        //         Таймаут 5 секунд. Если Open-Meteo ответил ошибкой (4xx/5xx) — бросить исключение.

        // TODO 2: достать из ответа массив 'results'.
        //         Если город не найден, ключа 'results' в ответе НЕТ — тогда пустой массив.

        // TODO 3: превратить каждый элемент results в new LocationDto(...) и вернуть список.
        //         Поле 'country' у некоторых мест отсутствует — передавайте null.
    }
}
