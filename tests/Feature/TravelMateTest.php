<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class TravelMateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_and_login()
    {
        $response = $this->postJson('/api/v1/register', [
            'name'=>'Test User',
            'email'=>'test@example.com',
            'password'=>'password123',
            'password_confirmation'=>'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['status','message','data'=>['token','user']]);

        $login = $this->postJson('/api/v1/login', [
            'email'=>'test@example.com',
            'password'=>'password123'
        ]);

        $login->assertStatus(200)
              ->assertJsonStructure(['status','message','data'=>['token','user']]);
    }

    /** @test */
    public function user_can_view_tourism_places()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tourism-places');

        $response->assertStatus(200)
                 ->assertJsonStructure(['status','data']);
    }
}