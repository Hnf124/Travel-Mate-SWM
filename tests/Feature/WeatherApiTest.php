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

        config([
            'services.openweather.key' => 'test-api-key',
        ]);

        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'name' => 'Banda Aceh',
                'main' => [
                    'temp' => 30.5,
                    'humidity' => 75,
                ],
                'weather' => [
                    [
                        'description' => 'cerah berawan',
                    ],
                ],
                'wind' => [
                    'speed' => 3.2,
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
                    'condition' => 'cerah berawan',
                    'humidity' => 75,
                    'wind_speed' => 3.2,
                ],
            ]);

        Http::assertSent(function (Request $request): bool {
            return $request->url()
                === 'https://api.openweathermap.org/data/2.5/weather?q=Banda%20Aceh&appid=test-api-key&units=metric&lang=id';
        });

        Http::assertSentCount(1);
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

    public function test_weather_returns_not_found_when_external_api_fails(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        config([
            'services.openweather.key' => 'test-api-key',
        ]);

        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'cod' => '404',
                'message' => 'city not found',
            ], 404),
        ]);

        $response = $this->getJson(
            '/api/v1/weather?city=KotaTidakAda'
        );

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'City not found or API error',
            ]);

        Http::assertSentCount(1);
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

    public function test_weather_uses_requested_city_when_api_response_has_no_city_name(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        config([
            'services.openweather.key' => 'test-api-key',
        ]);

        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'main' => [
                    'temp' => 28,
                    'humidity' => 80,
                ],
                'weather' => [
                    [
                        'description' => 'hujan ringan',
                    ],
                ],
                'wind' => [
                    'speed' => 2.5,
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
                    'condition' => 'hujan ringan',
                    'humidity' => 80,
                    'wind_speed' => 2.5,
                ],
            ]);
    }
}