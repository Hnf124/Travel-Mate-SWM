<?php

namespace Tests\Unit;

use App\Services\OpenMeteoWeatherService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenMeteoWeatherServiceTest extends TestCase
{
    public function test_it_fetches_weather_successfully(): void
    {
        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'name' => 'Banda Aceh',
                        'latitude' => 5.5577,
                        'longitude' => 95.3222,
                    ],
                ],
            ], 200),

            'https://api.open-meteo.com/*' => Http::response([
                'current' => [
                    'temperature_2m' => 30.5,
                    'relative_humidity_2m' => 75,
                    'weather_code' => 2,
                    'wind_speed_10m' => 3.2,
                ],
            ], 200),
        ]);

        $service = new OpenMeteoWeatherService();

        $result = $service->getCurrentWeatherByCity('Banda Aceh');

        $this->assertEquals('Banda Aceh', $result['city']);
        $this->assertEquals(30.5, $result['temperature']);
    }

    public function test_it_throws_exception_when_geocoding_fails(): void
    {
        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response(null, 500),
        ]);

        $service = new OpenMeteoWeatherService();

        $this->expectException(\App\Exceptions\WeatherServiceException::class);

        $service->getCurrentWeatherByCity('Banda Aceh');
    }

    public function test_it_throws_exception_when_weather_api_fails(): void
    {
        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'name' => 'Banda Aceh',
                        'latitude' => 5.5577,
                        'longitude' => 95.3222,
                    ],
                ],
            ], 200),

            'https://api.open-meteo.com/*' => Http::response(null, 500),
        ]);

        $service = new OpenMeteoWeatherService();

        $this->expectException(\App\Exceptions\WeatherServiceException::class);

        $service->getCurrentWeatherByCity('Banda Aceh');
    }
}