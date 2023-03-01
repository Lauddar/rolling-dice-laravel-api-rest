<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the game.
     */
    public function player()
    {
        return $this->belongsTo(User::class);
    }
}
