<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('user')->get();    

        return response(['players' => $players]);
    }
}
