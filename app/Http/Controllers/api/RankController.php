<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\DTO\UserDTO;
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
        $users = User::whereHas('games')
            ->withCount('games')
            ->with('games')
            ->orderByDesc('success_rate')
            ->orderByDesc('games_count')
            ->get();


        $userDTOs = [];
        foreach ($users as $user) {
            $userDTO = new UserDTO($user->id, $user->nickname, $user->email, $user->success_rate);
            $userDTOs[] = $userDTO;
        }

        $rank = new Rank;

        return response()->json(['users' => $userDTOs, 'averageSuccessRate' => $rank->averageSuccesRate()]);
    }

    /**
     * Returns the user with the lowest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loser()
    {
        $loser = User::whereHas('games')
            ->withCount('games')
            ->with('games')
            ->orderBy('success_rate')
            ->orderBy('games_count')
            ->first();

        $userDTO = new UserDTO($loser->id, $loser->nickname, $loser->email, $loser->success_rate);

        $games = $loser->games()->count();

        return response()->json(['user' => [$userDTO], 'games' => $games]);
    }

    /**
     * Returns the user with the highest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function winner()
    {
        $winner = User::whereHas('games')
            ->withCount('games')
            ->with('games')
            ->orderByDesc('success_rate')
            ->orderByDesc('games_count')
            ->first();

        $userDTO = new UserDTO($winner->id, $winner->nickname, $winner->email, $winner->success_rate);
        $games = $winner->games()->count();

        return response()->json(['user' => [$userDTO], 'games' => $games]);
    }
}
