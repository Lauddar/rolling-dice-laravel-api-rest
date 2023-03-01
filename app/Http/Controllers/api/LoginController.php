<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class LoginController extends Controller
{
    use HasApiTokens;

    /**
     * @method POST
     */
    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response(['message' => 'Invalid login credentials.'], 401);
        }

        $accesToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'acces_token' => $accesToken]);
    }
}
