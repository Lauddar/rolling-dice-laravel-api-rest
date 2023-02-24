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
        return response(['games' => $user->games->all()]);
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

        // Roll de dice
        $this->throwDices($game);

        // Save the game to the database.
        $game->save();

        // Update the user's success rate.
        $user->succesRate();

        // Response
        return response()->json([
            'message' => 'Game created succesfully.',
            'game' => $game
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
    public function throwDices(Game $game)
    {
        $game->first_dice = rand(1, 6);
        $game->second_dice = rand(1, 6);

        // Decides if player wins or loses
        if (($game->first_dice + $game->second_dice) == 7) {
            $game->victory = true;
        } else {
            $game->victory = false;
        }
    }
}
