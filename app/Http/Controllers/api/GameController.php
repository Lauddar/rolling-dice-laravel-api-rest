<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Get a list of all users' games.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return response(['games' => $user->games->all(), 'sucess_rate' => $user->succes_rate]);
    }


    /**
     * Starts a new game for the given user.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function play(User $user)
    {
        // Create a new game for the user.
        $game = new Game;
        $game->user_id = $user->id;

        $this->throwDice($game);

        // Save the game to the database.
        $game->save();

        $user->updateSuccessRate();

        // Response
        return response()->json([
            'message' => 'Game created successfully.',
            'game' => $game,
            'success_rate' => $user->success_rate,
        ], Response::HTTP_CREATED);
    }

    /**
     * Deletes all games for the given user.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(User $user)
    {
        $user->games()->delete();

        return response()->json([
            'message' => 'All games deleted succesfully.',
        ]);
    }

    /**
     * Rolls two dice and updates the game values accordingly.
     *
     * @param Game $game
     * The game in which the dice are being rolled.
     */
    public function throwDice(Game $game)
    {
        $game->first_dice = rand(1, 6);
        $game->second_dice = rand(1, 6);

        // Decides if player wins or loses
        $game->victory = ($game->firstDice + $game->secondDice == 7);
    }
}
