<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nickname',
        'email',
        'password',
        'success_rate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
        $victories = $this->games()->where('victory', 1)->count();
        $totalGames = $this->games()->count();

        if ($victories > 0) {
            $successRate = ($victories / $totalGames) * 100;
        } else {
            $successRate = 0.00;
        }

        return $successRate;
    }
}
