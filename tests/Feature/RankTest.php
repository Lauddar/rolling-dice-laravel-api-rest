<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RankTest extends TestCase
{
    use DatabaseTransactions;

    public function testRankAdmin()
    {
        $user = User::factory()->create()->assignRole(['Admin']);
        $token = $user->createToken('TestToken')->accessToken;

        $gameA = Game::factory()->create();
        $gameB = Game::factory()->create();
        $gameC = Game::factory()->create();

        $gameA->user()->update(['success_rate' => 80.14]);
        $gameB->user()->update(['success_rate' => 10.50]);
        $gameC->user()->update(['success_rate' => 62.14]);

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players/ranking');
    
        $response->assertOk();
    
        $response->assertJson([
                'averageSuccessRate' => 50.93,
            ]);
    }

    public function testRankPlayer()
    {
        $user = User::factory()->create()->assignRole(['Player']);
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players/ranking');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testLoserAdmin()
    {
        $user = User::factory()->create()->assignRole(['Admin']);
        $token = $user->createToken('TestToken')->accessToken;

        $gameA = Game::factory()->create();
        $gameB = Game::factory()->create();
        $gameC = Game::factory()->create();

        $gameA->user()->update(['success_rate' => 80.14]);
        $gameB->user()->update(['success_rate' => 10.50]);
        $gameC->user()->update(['success_rate' => 62.14]);

        $loser = $gameB->user;

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players/ranking/loser');
    
        $response->assertOk();
    
        $response->assertJson([
                'loser' => $loser->toArray(),
            ]);
    }

    public function testWinnerAdmin()
    {
        $user = User::factory()->create()->assignRole(['Admin']);
        $token = $user->createToken('TestToken')->accessToken;

        $gameA = Game::factory()->create();
        $gameB = Game::factory()->create();
        $gameC = Game::factory()->create();

        $gameA->user()->update(['success_rate' => 80.14]);
        $gameB->user()->update(['success_rate' => 10.50]);
        $gameC->user()->update(['success_rate' => 62.14]);

        $winner = $gameA->user;

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players/ranking/winner');
    
        $response->assertOk();
    
        $response->assertJson([
                'winner' => $winner->toArray(),
            ]);
    }



}