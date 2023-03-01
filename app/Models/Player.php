<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'success_rate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the games for the user.
     */
    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function updateSuccessRate()
    {
        $successRate = $this->calculateSuccessRate();
        $formattedSuccessRate = number_format((float)$successRate, 2, '.', '');

        $this->update(['success_rate' => $formattedSuccessRate]);
    }

    public function calculateSuccessRate()
    {
        $victories = $this->games()->where('victory', true)->count();
        $totalGames = $this->games()->count();

        if ($victories > 0) {
            $successRate = ($victories / $totalGames) * 100;
        } else {
            $successRate = 0;
        }

        return $successRate;
    }
}
