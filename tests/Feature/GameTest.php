<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GameTest extends TestCase
{
    use DatabaseTransactions;

    public function testPlay()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post("/api/players/{$user->id}/games");

        $response->assertStatus(Response::HTTP_CREATED);

        // Check that the game was created for the user
        $game = Game::where('user_id', $user->id)->first();
        $this->assertNotNull($game);

        // Check the response data
        $response->assertJson([
            'message' => 'Game created successfully.',
            'game' => $game->toArray(),
            'success_rate' => $game->user->success_rate,
        ]);
    }

    public function testWithoutToken()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->post("/api/players/{$user->id}/games");

        $response->assertStatus(Response::HTTP_FOUND);
    }

    public function testWithInvalidToken()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . 'xx',
        ])->post("/api/players/{$user->id}/games");

        $response->assertStatus(Response::HTTP_FOUND);
    }

    public function testDelete()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->accessToken;

        $games = Game::factory()->count(5)->create(['user_id' => $user->id]);
        $this->assertEquals(5, $user->games()->count());

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete("/api/players/{$user->id}/games");

        $response->assertOk();

        $this->assertEquals(0, $user->games()->count());

        $this->assertEquals(0, $user->games()->count());
    }

    public function testGamesIndex()
    {
        $user = User::factory()->create();
        $games = Game::factory()->count(3)->create(['user_id' => $user->id]);
        $games = Game::factory()->count(3)->create();
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post("/api/players/{$user->id}/games");

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJsonCount(3);
    }
}
