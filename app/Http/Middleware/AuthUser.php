<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthUser
{
    /**
     * Handle an incoming request. Check if request user is the same as parameter user.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = $request->route('user');
        
        if ($request->user()->id !== $user->id) {
            return response()->json([
                'result' => [
                    'message' => 'Unauthorized',
                ],
                'status' => false,
            ]);
        }

        return $next($request);
    }
}
