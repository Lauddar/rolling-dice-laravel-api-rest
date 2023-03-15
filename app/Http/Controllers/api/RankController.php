<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\User;

class RankController extends Controller
{
    /**
     * Returns the average success rate of all users in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function rank()
    {
        $rank = new Rank;
        return response()->json(['averageSuccessRate' => $rank->averageSuccesRate()]);
    }

    /**
     * Returns the user with the lowest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loser()
    {
        $loser = User::whereHas('games')->with('games')->orderBy('success_rate')->first();

        return response()->json(['players' => [$loser]]);
    }

    /**
     * Returns the user with the highest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function winner()
    {
        $winner = User::whereHas('games')->with('games')->orderBy('success_rate', 'desc')->first();

        return response()->json(['players' => [$winner]]);
    }
}
