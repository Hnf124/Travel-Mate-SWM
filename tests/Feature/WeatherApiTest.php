<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_weather_data(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'id' => 1215502,
                        'name' => 'Banda Aceh',
                        'latitude' => 5.5577,
                        'longitude' => 95.3222,
                        'country_code' => 'ID',
                        'country' => 'Indonesia',
                    ],
                ],
            ], 200),

            'https://api.open-meteo.com/*' => Http::response([
                'latitude' => 5.55,
                'longitude' => 95.32,
                'current' => [
                    'temperature_2m' => 30.5,
                    'relative_humidity_2m' => 75,
                    'weather_code' => 2,
                    'wind_speed_10m' => 3.2,
                ],
            ], 200),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=Banda%20Aceh'
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'city',
                    'temperature',
                    'condition',
                    'humidity',
                    'wind_speed',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'city' => 'Banda Aceh',
                    'temperature' => 30.5,
                    'condition' => 'Berawan sebagian',
                    'humidity' => 75,
                    'wind_speed' => 3.2,
                ],
            ]);

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            if (
                !str_starts_with(
                    $url,
                    'https://geocoding-api.open-meteo.com/v1/search'
                )
            ) {
                return false;
            }

            parse_str(
                (string) parse_url($url, PHP_URL_QUERY),
                $query
            );

            return ($query['name'] ?? null) === 'Banda Aceh'
                && ($query['count'] ?? null) === '1'
                && ($query['language'] ?? null) === 'id'
                && ($query['format'] ?? null) === 'json'
                && ($query['countryCode'] ?? null) === 'ID';
        });

        Http::assertSent(function (Request $request): bool {
            $url = $request->url();

            if (
                !str_starts_with(
                    $url,
                    'https://api.open-meteo.com/v1/forecast'
                )
            ) {
                return false;
            }

            parse_str(
                (string) parse_url($url, PHP_URL_QUERY),
                $query
            );

            return (float) ($query['latitude'] ?? 0) === 5.5577
                && (float) ($query['longitude'] ?? 0) === 95.3222
                && ($query['current'] ?? null)
                    === 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m'
                && ($query['wind_speed_unit'] ?? null) === 'ms'
                && ($query['timezone'] ?? null) === 'auto';
        });

        Http::assertSentCount(2);
    }

    public function test_weather_requires_city_parameter(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Http::fake();

        $response = $this->getJson('/api/v1/weather');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'city',
            ]);

        Http::assertNothingSent();
    }

    public function test_weather_returns_not_found_when_city_does_not_exist(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [],
            ], 200),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=KotaTidakAda'
        );

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Kota tidak ditemukan',
            ]);

        Http::assertSentCount(1);
    }

    public function test_weather_returns_bad_gateway_when_geocoding_api_fails(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response([
                'message' => 'Service unavailable',
            ], 503),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=Banda%20Aceh'
        );

        $response
            ->assertStatus(502)
            ->assertJson([
                'status' => 'error',
                'message' => 'Gagal mencari lokasi kota',
            ]);

        Http::assertSentCount(1);
    }

    public function test_weather_returns_bad_gateway_when_weather_api_fails(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

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
                'message' => 'Weather service unavailable',
            ], 503),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=Banda%20Aceh'
        );

        $response
            ->assertStatus(502)
            ->assertJson([
                'status' => 'error',
                'message' => 'Gagal mengambil data cuaca',
            ]);

        Http::assertSentCount(2);
    }

    public function test_weather_returns_bad_gateway_when_current_weather_is_missing(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

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
                'latitude' => 5.55,
                'longitude' => 95.32,
            ], 200),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=Banda%20Aceh'
        );

        $response
            ->assertStatus(502)
            ->assertJson([
                'status' => 'error',
                'message' => 'Data cuaca tidak tersedia',
            ]);

        Http::assertSentCount(2);
    }

    public function test_unauthenticated_user_cannot_access_weather(): void
    {
        Http::fake();

        $response = $this->getJson(
            '/api/v1/weather?city=Banda%20Aceh'
        );

        $response->assertStatus(401);

        Http::assertNothingSent();
    }

    public function test_weather_uses_requested_city_when_geocoding_response_has_no_name(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Http::fake([
            'https://geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'latitude' => 3.5952,
                        'longitude' => 98.6722,
                    ],
                ],
            ], 200),

            'https://api.open-meteo.com/*' => Http::response([
                'current' => [
                    'temperature_2m' => 28,
                    'relative_humidity_2m' => 80,
                    'weather_code' => 61,
                    'wind_speed_10m' => 2.5,
                ],
            ], 200),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=Medan'
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'city' => 'Medan',
                    'temperature' => 28,
                    'condition' => 'Hujan',
                    'humidity' => 80,
                    'wind_speed' => 2.5,
                ],
            ]);

        Http::assertSentCount(2);
    }
}