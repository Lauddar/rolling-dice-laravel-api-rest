<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get a list of all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(['players' => User::all()]);
    }

    public function getUser(User $user)
    {
        return response(['user' => User::find($user)]);
    }

    /**
     * Create a new user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Input validation
        try {
            $validatedData  = $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'email.unique' => 'The email is already in use',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            // Check if nickname already exists
            if (isset($request->nickname)) {
                if (User::where('nickname', $request->nickname)->first()) {
                    return response()->json(['result' => ['message' => 'Operation failed. This nickname is already taken.'], 'satatus' => false], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $nickname = $request->nickname;
            } else {
                $nickname = 'anonymous';
            }

            // Create a new user.
            $user = User::create([
                'nickname' => $nickname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'success_rate' => 0.00,
            ]);

            // Response
            return response()->json([
                'result' => [
                    'message' => 'User created succesfully.',
                    'user' => $user,
                ],
                'satatus' => true
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['result' => ['message' => $e->getMessage()], 'satatus' => false], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update user's nickname.
     *
     * @param  Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        // Get the new nickname from the request
        $nickname = $request->input('nickname');

        // Input validation not null.
        if (!isset($nickname)) {
            $user->update(['nickname' => 'anonymous']);
            return response()->json([
                'result' => [
                    'message' => 'Nickname updated succesfully.',
                    'user' => $user,
                ],
                'status' => true,
            ]);
        }

        // If the nickname is not set, return an error response, else, update nickname.
        if (!User::where('nickname', $nickname)->first()) {
            $user->update(['nickname' => $nickname]);
            return response()->json([
                'result' => [
                    'message' => 'Nickname updated succesfully.',
                    'user' => $user,
                ],
                'status' => true,
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'result' => [
                    'message' => 'Nickname cannot be updated because it is already taken.',
                    'user' => $user,
                ],
                'status' => false,
            ]);
        }
    }
}
