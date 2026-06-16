<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\TourismPlace;
use App\Models\User;
use Database\Seeders\TourismPlacesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected TourismPlace $tourismPlace;

    protected function setUp(): void
    {
        parent::setUp();

        // Membuat user untuk pengujian
        $this->user = User::factory()->create();

        // Menjalankan seeder tempat wisata
        $this->seed(TourismPlacesSeeder::class);

        // Mengambil satu tempat wisata untuk kebutuhan pengujian
        $this->tourismPlace = TourismPlace::query()->firstOrFail();
    }

    public function test_authenticated_user_can_add_tourism_place_to_favorites(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/favorites', [
            'tourism_place_id' => $this->tourismPlace->id,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Added to favorites',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'tourism_place_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'tourism_place_id' => $this->tourismPlace->id,
        ]);
    }

    public function test_authenticated_user_can_get_favorite_list(): void
    {
        Sanctum::actingAs($this->user);

        Favorite::query()->create([
            'user_id' => $this->user->id,
            'tourism_place_id' => $this->tourismPlace->id,
        ]);

        $response = $this->getJson('/api/v1/favorites');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'tourism_place_id',
                        'tourism_place',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'user_id' => $this->user->id,
                        'tourism_place_id' => $this->tourismPlace->id,
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_delete_favorite(): void
    {
        Sanctum::actingAs($this->user);

        $favorite = Favorite::query()->create([
            'user_id' => $this->user->id,
            'tourism_place_id' => $this->tourismPlace->id,
        ]);

        $response = $this->deleteJson(
            "/api/v1/favorites/{$this->tourismPlace->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Removed from favorites',
            ]);

        $this->assertDatabaseMissing('favorites', [
            'id' => $favorite->id,
            'user_id' => $this->user->id,
            'tourism_place_id' => $this->tourismPlace->id,
        ]);
    }

    public function test_user_cannot_add_same_tourism_place_to_favorites_twice(): void
    {
        Sanctum::actingAs($this->user);

        $firstResponse = $this->postJson('/api/v1/favorites', [
            'tourism_place_id' => $this->tourismPlace->id,
        ]);

        $firstResponse
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Added to favorites',
            ]);

        $secondResponse = $this->postJson('/api/v1/favorites', [
            'tourism_place_id' => $this->tourismPlace->id,
        ]);

        $secondResponse
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Already in favorites',
            ]);

        $this->assertDatabaseCount('favorites', 1);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'tourism_place_id' => $this->tourismPlace->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_favorites(): void
    {
        $getResponse = $this->getJson('/api/v1/favorites');

        $getResponse->assertStatus(401);

        $postResponse = $this->postJson('/api/v1/favorites', [
            'tourism_place_id' => $this->tourismPlace->id,
        ]);

        $postResponse->assertStatus(401);

        $deleteResponse = $this->deleteJson(
            "/api/v1/favorites/{$this->tourismPlace->id}"
        );

        $deleteResponse->assertStatus(401);

        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_user_cannot_add_invalid_tourism_place_to_favorites(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/favorites', [
            'tourism_place_id' => 999999,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'tourism_place_id',
            ]);

        $this->assertDatabaseCount('favorites', 0);
    }
}