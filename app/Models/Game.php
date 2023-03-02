<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id'
    ];

    /**
     * One to many relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
