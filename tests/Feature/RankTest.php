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

    /**
     * @test
     * Test rank() returns the average succes rate from all players only for Admin role.
     */
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

        // Assert that the response is successful
        $response->assertOk();

        // The first player in the response should be the one with the highest success rate (gameA)
        $this->assertEquals(80.14, $response['users'][0]['success_rate']);
        $this->assertEquals($gameA->user->nickname, $response['users'][0]['nickname']);

        // The last player in the response should be the one with the lowest success rate (gameB)
        $this->assertEquals(10.50, $response['users'][2]['success_rate']);
        $this->assertEquals($gameB->user->nickname, $response['users'][2]['nickname']);
    }

    /**
     * @test
     * Test loser() gives the player with lower succes rate, only for Admin role.
     */
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
            'user' => [
                [
                    'nickname' => $loser->nickname,
                    'email' => $loser->email,
                    'success_rate' => round($loser->success_rate, 2),
                    'games' => 1,
                ]
            ]
        ]);
    }

    /**
     * @test
     * Test winner() gives the player with higher succes rate, only for Admin role.
     */
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
            'user' => [
                [
                    'nickname' => $winner->nickname,
                    'email' => $winner->email,
                    'success_rate' => round($winner->success_rate, 2),
                    'games' => 1,
                ]
            ],
        ]);
    }
}
