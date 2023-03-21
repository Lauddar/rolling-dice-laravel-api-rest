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
        $users = $this->getRank();

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'nickname' => $user->nickname,
                'email' => $user->email,
                'success_rate' => $user->success_rate,
                'games' => $user->games()->count(),
            ];
        }

        $rank = new Rank;

        return response()->json(['users' => $userData, 'averageSuccessRate' => $rank->averageSuccesRate()]);
    }

    /**
     * Returns the user with the lowest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loser()
    {
        $loser = $this->getRank()->last();

        $games = $loser->games()->count();

        return response()->json(['user' => [['email' => $loser->email, 'nickname' => $loser->nickname, 'success_rate' => $loser->success_rate, 'games' => $games]]]);
    }

    /**
     * Returns the user with the highest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function winner()
    {
        $winner = $this->getRank()->first();

        $games = $winner->games()->count();

        return response()->json(['user' => [['email' => $winner->email, 'nickname' => $winner->nickname, 'success_rate' => $winner->success_rate, 'games' => $games]]]);
    }

    /**
     * Returns the rank of users based on their success rate and number of games played
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\User[]
     */
    private function getRank()
    {
        $rank = User::whereHas('games')
            ->withCount('games')
            ->with('games')
            ->orderByDesc('success_rate')
            ->orderByDesc('games_count')
            ->get();
        return $rank;
    }
}
