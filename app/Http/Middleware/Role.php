<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
    
        return $next($request);
    }
}
