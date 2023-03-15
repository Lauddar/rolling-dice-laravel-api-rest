<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    /**
     * Calculate the average success rate for all users.
     *
     * @return string The average success rate.
     */
    public static function averageSuccesRate()
    {
        $averageSuccessRate = 0;

        //Check if there are games in database and calculate average success rate
        if (Game::exists()) {
            $succesRates = User::whereHas('games')->with('games')->pluck('success_rate');


            foreach ($succesRates as $succesRate) {
                $averageSuccessRate +=  $succesRate;
            }

            $averageSuccessRate = number_format($averageSuccessRate / $succesRates->count(), 2, '.', '');
        }

        return $averageSuccessRate;
    }
}
