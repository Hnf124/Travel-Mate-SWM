<?php

namespace App\Services;

use App\Contracts\WeatherServiceInterface;
use App\Exceptions\WeatherServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OpenMeteoWeatherService implements WeatherServiceInterface
{
    private const USER_AGENT = 'TravelMate/1.0 (+https://github.com/Hnf124/Travel-Mate-SWM)';

    /**
     * Koordinat cadangan untuk kota yang digunakan pada data demo.
     */
    private const FALLBACK_COORDINATES = [
        'banda aceh' => [
            'name' => 'Banda Aceh',
            'latitude' => 5.5483,
            'longitude' => 95.3238,
        ],
    ];

    public function getCurrentWeatherByCity(string $city): array
    {
        $city = Str::squish($city);
        $cacheKey = 'weather:'.sha1(Str::lower($city));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($city): array {
            $location = $this->resolveLocation($city);

            $openMeteoWeather = $this->getFromOpenMeteo($location);

            if ($openMeteoWeather !== null) {
                return $openMeteoWeather;
            }

            return $this->getFromMetNorway($location);
        });
    }

    private function resolveLocation(string $city): array
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent(self::USER_AGENT)
                ->timeout(12)
                ->get('https://geocoding-api.open-meteo.com/v1/search', [
                    'name' => $city,
                    'count' => 1,
                    'language' => 'id',
                    'format' => 'json',
                    'countryCode' => 'ID',
                ]);

            if ($response->successful()) {
                $location = $response->json('results.0');

                if ($location && isset($location['latitude'], $location['longitude'])) {
                    return [
                        'name' => $location['name'] ?? $city,
                        'latitude' => (float) $location['latitude'],
                        'longitude' => (float) $location['longitude'],
                    ];
                }
            }

            Log::warning('Open-Meteo geocoding gagal.', [
                'city' => $city,
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Open-Meteo geocoding tidak dapat dihubungi.', [
                'city' => $city,
                'message' => $exception->getMessage(),
            ]);
        }

        $fallback = self::FALLBACK_COORDINATES[Str::lower($city)] ?? null;

        if ($fallback !== null) {
            return $fallback;
        }

        throw new WeatherServiceException('Kota tidak ditemukan', 404);
    }

    private function getFromOpenMeteo(array $location): ?array
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent(self::USER_AGENT)
                ->timeout(12)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => round((float) $location['latitude'], 4),
                    'longitude' => round((float) $location['longitude'], 4),
                    'current' => implode(',', [
                        'temperature_2m',
                        'relative_humidity_2m',
                        'weather_code',
                        'wind_speed_10m',
                    ]),
                    'wind_speed_unit' => 'ms',
                    'timezone' => 'auto',
                ]);

            if (! $response->successful()) {
                Log::warning('Open-Meteo forecast gagal, memakai provider cadangan.', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                ]);

                return null;
            }

            $current = $response->json('current');

            if (! is_array($current)) {
                Log::warning('Open-Meteo tidak mengembalikan current weather.');
                return null;
            }

            $weatherCode = (int) ($current['weather_code'] ?? -1);

            return [
                'city' => $location['name'],
                'temperature' => $current['temperature_2m'] ?? null,
                'condition' => $this->getOpenMeteoDescription($weatherCode),
                'humidity' => $current['relative_humidity_2m'] ?? null,
                'wind_speed' => $current['wind_speed_10m'] ?? null,
            ];
        } catch (ConnectionException $exception) {
            Log::warning('Open-Meteo forecast tidak dapat dihubungi, memakai provider cadangan.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function getFromMetNorway(array $location): array
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent(self::USER_AGENT)
                ->timeout(12)
                ->get('https://api.met.no/weatherapi/locationforecast/2.0/compact', [
                    'lat' => round((float) $location['latitude'], 4),
                    'lon' => round((float) $location['longitude'], 4),
                ]);

            if (! $response->successful()) {
                Log::error('MET Norway forecast gagal.', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);

                throw new WeatherServiceException('Gagal mengambil data cuaca', 502);
            }

            $item = $response->json('properties.timeseries.0');
            $details = data_get($item, 'data.instant.details');

            if (! is_array($details)) {
                throw new WeatherServiceException('Data cuaca tidak tersedia', 502);
            }

            $symbolCode =
                data_get($item, 'data.next_1_hours.summary.symbol_code')
                ?? data_get($item, 'data.next_6_hours.summary.symbol_code')
                ?? data_get($item, 'data.next_12_hours.summary.symbol_code')
                ?? '';

            return [
                'city' => $location['name'],
                'temperature' => $details['air_temperature'] ?? null,
                'condition' => $this->getMetNorwayDescription((string) $symbolCode),
                'humidity' => $details['relative_humidity'] ?? null,
                'wind_speed' => $details['wind_speed'] ?? null,
            ];
        } catch (ConnectionException $exception) {
            Log::error('MET Norway tidak dapat dihubungi.', [
                'message' => $exception->getMessage(),
            ]);

            throw new WeatherServiceException('Tidak dapat terhubung ke layanan cuaca', 502);
        }
    }

    private function getOpenMeteoDescription(int $weatherCode): string
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

    private function getMetNorwayDescription(string $symbolCode): string
    {
        $symbol = preg_replace('/_(day|night|polartwilight)$/', '', $symbolCode) ?? $symbolCode;

        return match (true) {
            str_contains($symbol, 'clearsky') => 'Cerah',
            str_contains($symbol, 'fair') => 'Sebagian besar cerah',
            str_contains($symbol, 'partlycloudy') => 'Berawan sebagian',
            str_contains($symbol, 'cloudy') => 'Mendung',
            str_contains($symbol, 'fog') => 'Berkabut',
            str_contains($symbol, 'heavyrain') => 'Hujan lebat',
            str_contains($symbol, 'rain') => 'Hujan',
            str_contains($symbol, 'heavysleet') => 'Hujan es lebat',
            str_contains($symbol, 'sleet') => 'Hujan es',
            str_contains($symbol, 'heavysnow') => 'Salju lebat',
            str_contains($symbol, 'snow') => 'Salju',
            str_contains($symbol, 'thunder') => 'Badai petir',
            default => 'Kondisi cuaca tidak diketahui',
        };
    }
}
