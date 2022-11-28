<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if (empty(\Auth::guard('api')->user())) {
            return response()->json(['message' => 'You are not logged in!', 'data' => ['general' => 'You are not logged in!']], 401);
        }

        return $next($request);
    }
}
