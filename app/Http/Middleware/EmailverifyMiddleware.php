<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EmailverifyMiddleware
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
        // Check if the user is authenticated
        if (Auth::check() && Auth::user()->email_verified_at === null) {
            // Redirect to a specific route or send a message
            return redirect('user/email/verification')->with('status', 'Please verify your email address.');

        }

        return $next($request);
    }
}
