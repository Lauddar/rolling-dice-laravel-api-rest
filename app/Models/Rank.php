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
        $succesRates = User::whereHas('games')->with('games')->pluck('success_rate');
        $averageSuccesRate = 0;

        foreach ($succesRates as $succesRate) {
            $averageSuccesRate +=  $succesRate;
        }

        $averageSuccesRate = number_format($averageSuccesRate / $succesRates->count(), 2, '.', '');

        return $averageSuccesRate;
    }
}
