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
    public function play(User $user){
        $game = new Game;
        $game->user_id = $user->id;
        $game->first_dice = rand(1,6);
        $game->second_dice = rand(1,6);
        
        if(($game->first_dice + $game->second_dice) == 7) {
            $game->victory = true;
        } else {
            $game->victory = false;
        }

        $game = $game->save();

        return response()->json([
            'message' => 'Game created succesfully.',
            'game' => $game
        ], Response::HTTP_CREATED);
    }
}
