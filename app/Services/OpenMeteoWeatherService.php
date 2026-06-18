<?php

namespace App\Services;

use App\Contracts\WeatherServiceInterface;
use App\Exceptions\WeatherServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenMeteoWeatherService implements WeatherServiceInterface
{
    public function getCurrentWeatherByCity(string $city): array
    {
        try {
            $locationResponse = Http::timeout(10)->get(
                'https://geocoding-api.open-meteo.com/v1/search',
                [
                    'name' => $city,
                    'count' => 1,
                    'language' => 'id',
                    'format' => 'json',
                    'countryCode' => 'ID',
                ]
            );

            if (! $locationResponse->successful()) {
                throw new WeatherServiceException('Gagal mencari lokasi kota', 502);
            }

            $location = $locationResponse->json('results.0');

            if (! $location) {
                throw new WeatherServiceException('Kota tidak ditemukan', 404);
            }

            $weatherResponse = Http::timeout(10)->get(
                'https://api.open-meteo.com/v1/forecast',
                [
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'current' => implode(',', [
                        'temperature_2m',
                        'relative_humidity_2m',
                        'weather_code',
                        'wind_speed_10m',
                    ]),
                    'wind_speed_unit' => 'ms',
                    'timezone' => 'auto',
                ]
            );

            if (! $weatherResponse->successful()) {
                throw new WeatherServiceException('Gagal mengambil data cuaca', 502);
            }

            $currentWeather = $weatherResponse->json('current');

            if (! $currentWeather) {
                throw new WeatherServiceException('Data cuaca tidak tersedia', 502);
            }

            $weatherCode = (int) ($currentWeather['weather_code'] ?? -1);

            return [
                'city' => $location['name'] ?? $city,
                'temperature' => $currentWeather['temperature_2m'] ?? null,
                'condition' => $this->getWeatherDescription($weatherCode),
                'humidity' => $currentWeather['relative_humidity_2m'] ?? null,
                'wind_speed' => $currentWeather['wind_speed_10m'] ?? null,
            ];
        } catch (ConnectionException) {
            throw new WeatherServiceException('Tidak dapat terhubung ke layanan cuaca', 502);
        }
    }

    private function getWeatherDescription(int $weatherCode): string
    {
        return match ($weatherCode) {
            0 => 'Cerah',
            1 => 'Sebagian besar cerah',
            2 => 'Berawan sebagian',
            3 => 'Mendung',
            45, 48 => 'Berkabut',
            51, 53, 55 => 'Gerimis',
            56, 57 => 'Gerimis beku',
            61, 63, 65 => 'Hujan',
            66, 67 => 'Hujan beku',
            71, 73, 75 => 'Salju',
            77 => 'Butiran salju',
            80, 81, 82 => 'Hujan lokal',
            85, 86 => 'Hujan salju',
            95 => 'Badai petir',
            96, 99 => 'Badai petir disertai hujan es',
            default => 'Kondisi cuaca tidak diketahui',
        };
    }
}
