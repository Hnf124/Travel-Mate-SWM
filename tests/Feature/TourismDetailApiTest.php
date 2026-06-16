<?php

namespace Tests\Feature;

use App\Models\TourismPlace;
use App\Models\User;
use Database\Seeders\TourismPlacesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TourismDetailApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_tourism_place_detail(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->seed(TourismPlacesSeeder::class);

        $tourismPlace = TourismPlace::query()->firstOrFail();

        $response = $this->getJson(
            "/api/v1/tourism-places/{$tourismPlace->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $tourismPlace->id,
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_view_tourism_place_detail(): void
    {
        $this->seed(TourismPlacesSeeder::class);

        $tourismPlace = TourismPlace::query()->firstOrFail();

        $response = $this->getJson(
            "/api/v1/tourism-places/{$tourismPlace->id}"
        );

        $response->assertStatus(401);
    }

    public function test_authenticated_user_receives_not_found_for_invalid_tourism_place_id(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(
            '/api/v1/tourism-places/999999'
        );

        $response->assertStatus(404);
    }
}