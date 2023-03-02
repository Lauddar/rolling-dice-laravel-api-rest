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
     * Handle an incoming login request.
     *
     * @method POST
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Input validation
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        // Authenticate the user with the provided credentials
        if (!Auth::attempt($credentials)) {
            return response(['message' => 'Invalid login credentials.'], 401);
        }

        // Generate a new acces token for the user
        $accesToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'acces_token' => $accesToken]);
    }
}
