<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function testIndex()
    {

        $user = User::factory()->create()->assignRole([Role::create(['name' => 'Admin'])]);
        $token = $user->createToken('TestToken')->accessToken;
        
        $player1 = User::factory()->create()->player()->create(['success_rate' => fake()->randomFloat(2, 0, 100)]);
        $player2 = User::factory()->create()->player()->create(['success_rate' => fake()->randomFloat(2, 0, 100)]);
        $player3 = User::factory()->create()->player()->create(['success_rate' => fake()->randomFloat(2, 0, 100)]);

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players');

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(3, 'players')->assertJsonStructure(['players']);
    }
}
