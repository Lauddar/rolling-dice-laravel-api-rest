<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Http\Request;

class RankController extends Controller
{
    /**
     * Returns the average success rate of all users in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function rank()
    {
        return response()->json(['averageSuccesRate' => Rank::averageSuccesRate()]);
    }

    /**
     * Returns the user with the lowest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loser()
    {

        $loser = User::orderBy('succes_rate')->first();

        return response()->json(['loser' => $loser]);
    }

    /**
     * Returns the user with the highest success rate in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function winner()
    {

        $winner = User::orderBy('succes_rate', 'desc')->first();

        return response()->json(['loser' => $winner]);
    }

    /**
     * Calculate the average success rate for all users.
     *
     * @return float The average success rate.
     */
    public static function averageSuccesRate()
    {
        $succesRates = User::pluck('succes_rate');
        $averageSuccesRate = 0;

        foreach ($succesRates as $succesRate) {
            $averageSuccesRate +=  $succesRate;
        }

        $averageSuccesRate = $averageSuccesRate / $succesRates->count();

        return $averageSuccesRate;
    }
}
