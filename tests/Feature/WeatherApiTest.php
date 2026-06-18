<?php

namespace Tests\Feature;

use App\Contracts\WeatherServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    private $weatherMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->weatherMock = $this->mock(WeatherServiceInterface::class);
    }

    public function test_authenticated_user_can_get_weather_data(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->weatherMock
            ->shouldReceive('getCurrentWeatherByCity')
            ->once()
            ->with('Banda Aceh')
            ->andReturn([
                'city' => 'Banda Aceh',
                'temperature' => 30.5,
                'condition' => 'Berawan sebagian',
                'humidity' => 75,
                'wind_speed' => 3.2,
            ]);

        $response = $this->getJson('/api/v1/weather?city=Banda%20Aceh');

        $response->assertStatus(200)
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
    }

    public function test_weather_requires_city_parameter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/weather');

        $response->assertStatus(422);
    }

    public function test_weather_returns_not_found_when_city_does_not_exist(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->weatherMock
            ->shouldReceive('getCurrentWeatherByCity')
            ->once()
            ->with('KotaTidakAda')
            ->andThrow(new \App\Exceptions\WeatherServiceException('Kota tidak ditemukan', 404));

        $response = $this->getJson('/api/v1/weather?city=KotaTidakAda');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Kota tidak ditemukan',
            ]);
    }

    public function test_weather_handles_service_failure(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->weatherMock
            ->shouldReceive('getCurrentWeatherByCity')
            ->once()
            ->andThrow(new \App\Exceptions\WeatherServiceException('Service error', 502));

        $response = $this->getJson('/api/v1/weather?city=Banda%20Aceh');

        $response->assertStatus(502)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_weather(): void
    {
        $response = $this->getJson('/api/v1/weather?city=Banda%20Aceh');

        $response->assertStatus(401);
    }
}