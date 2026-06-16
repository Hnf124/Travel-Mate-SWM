<?php

namespace Tests\Feature;

use App\Models\TourismPlace;
use App\Models\User;
use Database\Seeders\TourismPlacesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TourismSearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->seed(TourismPlacesSeeder::class);

        TourismPlace::query()->create([
            'name' => 'Istana Maimun',
            'city' => 'Medan',
            'category' => 'Heritage',
            'address' => 'Jl. Brigjen Katamso, Medan',
            'description' => 'Istana bersejarah Kesultanan Deli di Kota Medan.',
            'short_description' => 'Istana bersejarah di Medan.',
            'image_url' => 'https://example.com/istana-maimun.jpg',
        ]);
    }

    public function test_authenticated_user_can_search_tourism_places_by_city(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(
            '/api/v1/tourism-places?city=Banda%20Aceh'
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(10, 'data');

        $responseData = $response->json('data');

        foreach ($responseData as $tourismPlace) {
            $this->assertSame(
                'Banda Aceh',
                $tourismPlace['city']
            );
        }

        $response->assertJsonMissing([
            'name' => 'Istana Maimun',
            'city' => 'Medan',
        ]);
    }

    public function test_city_search_supports_partial_city_name(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(
            '/api/v1/tourism-places?city=Banda'
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(10, 'data');

        $responseData = $response->json('data');

        foreach ($responseData as $tourismPlace) {
            $this->assertStringContainsString(
                'Banda',
                $tourismPlace['city']
            );
        }

        $response->assertJsonMissing([
            'name' => 'Istana Maimun',
        ]);
    }

    public function test_search_returns_empty_data_when_city_is_not_found(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(
            '/api/v1/tourism-places?city=Jakarta'
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [],
            ])
            ->assertJsonCount(0, 'data');
    }

    public function test_request_without_city_returns_all_tourism_places(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(
            '/api/v1/tourism-places'
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(11, 'data');

        $response->assertJsonFragment([
            'name' => 'Pantai Lampuuk',
            'city' => 'Banda Aceh',
        ]);

        $response->assertJsonFragment([
            'name' => 'Istana Maimun',
            'city' => 'Medan',
        ]);
    }

    public function test_unauthenticated_user_cannot_search_tourism_places(): void
    {
        $response = $this->getJson(
            '/api/v1/tourism-places?city=Banda%20Aceh'
        );

        $response->assertStatus(401);
    }
}