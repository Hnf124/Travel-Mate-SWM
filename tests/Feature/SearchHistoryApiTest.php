<?php

namespace Tests\Feature;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SearchHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_save_search_history(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/search-history', [
            'keyword' => 'Banda Aceh',
            'type' => 'city',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'keyword',
                    'type',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Search saved',
                'data' => [
                    'user_id' => $user->id,
                    'keyword' => 'Banda Aceh',
                    'type' => 'city',
                ],
            ]);

        $this->assertDatabaseHas('search_histories', [
            'user_id' => $user->id,
            'keyword' => 'Banda Aceh',
            'type' => 'city',
        ]);
    }

    public function test_authenticated_user_can_view_search_history(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        SearchHistory::query()->create([
            'user_id' => $user->id,
            'keyword' => 'Pantai',
            'type' => 'place',
        ]);

        $response = $this->getJson('/api/v1/search-history');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'keyword',
                        'type',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'user_id' => $user->id,
                        'keyword' => 'Pantai',
                        'type' => 'place',
                    ],
                ],
            ]);
    }

    public function test_search_history_requires_keyword_and_type(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/search-history', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'keyword',
                'type',
            ]);

        $this->assertDatabaseCount('search_histories', 0);
    }

    public function test_unauthenticated_user_cannot_access_search_history(): void
    {
        $getResponse = $this->getJson('/api/v1/search-history');

        $getResponse->assertStatus(401);

        $postResponse = $this->postJson('/api/v1/search-history', [
            'keyword' => 'Banda Aceh',
            'type' => 'city',
        ]);

        $postResponse->assertStatus(401);

        $this->assertDatabaseCount('search_histories', 0);
    }

    public function test_search_history_is_limited_to_twenty_entries_per_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for ($number = 1; $number <= 25; $number++) {
            $response = $this->postJson('/api/v1/search-history', [
                'keyword' => "Keyword {$number}",
                'type' => 'city',
            ]);

            $response->assertStatus(200);
        }

        $this->assertDatabaseCount('search_histories', 20);

        $this->assertDatabaseMissing('search_histories', [
            'user_id' => $user->id,
            'keyword' => 'Keyword 1',
        ]);

        $this->assertDatabaseHas('search_histories', [
            'user_id' => $user->id,
            'keyword' => 'Keyword 25',
        ]);

        $response = $this->getJson('/api/v1/search-history');

        $response
            ->assertStatus(200)
            ->assertJsonCount(20, 'data');
    }
}