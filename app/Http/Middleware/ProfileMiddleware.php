<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProfileMiddleware
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
        if (Auth::check() && Auth::user()->verified_by_admin == 0) {
            if (request()->expectsJson()) {
                // For AJAX request, return a JSON response
                return response()->json([
                    'error' => true,
                    'message' => 'Please wait for Approval From Admin.'
                ], 403);
            } else {
                // For normal request, redirect to a specific route
                return redirect('user/user-data')->with('error', 'Please wait for Approval Admin.');
            }
        }


        return $next($request);
    }
}
