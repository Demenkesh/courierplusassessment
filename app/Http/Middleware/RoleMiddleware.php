<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles): Response
    {
        // Ensure the user is authenticated via the admin guard
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        // Get the authenticated user
        $user = Auth::guard('admin')->user();
        // dd($user);

        // If the user is a super-admin, allow access to all routes
        if ($user->role_as === 'admin') {
            return $next($request);
        }

        // Check if the user is a 'supportticket' role, and allow specific routes

        // Check if the user is a 'supportticket' role, and allow specific routes
        if ($user->role_as === 'supportticket') {;
            if (Str::is('admin/ticket/*', $request->path())) {
                return $next($request);
            }
        }

        if ($user->role_as === 'subscriber') {
            // Use Str::is to check if the requested path matches the pattern
            if (Str::is('admin/subscriber/*', $request->path())) {
                return $next($request);  // Allow the request to proceed
            }
        }

        if ($user->role_as === 'managebooking') {
            $blockedRoutes = [
                'hotel/users/detail/*',      // Block any route matching this pattern
                'hotel/user/sendmail/*',     // Block any route matching this pattern
                'hotel/setting/about/image/*'
            ];
            foreach ($blockedRoutes as $route) {
                if (Str::is($route, $request->path())) {
                    return redirect('admin/dashboard')->with('error', 'Unauthorized action');
                }
            }
            $allowedRoutes = [
                'admin/hotel/*',              // Allowed for 'managebooking' role
                'admin/booking/*',            // Allowed for 'managebooking' role
                'admin/get-rooms-by-type',    // Allowed for 'managebooking' role
            ];
            // Allow access to the allowed routes
            foreach ($allowedRoutes as $allowedRoute) {
                if (Str::is($allowedRoute, $request->path())) {
                    return $next($request);
                }
            }
        }

        // If the user role is not one of the allowed roles, deny access
        if (!in_array($user->role_as, $roles)) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        return $next($request);
    }
}
