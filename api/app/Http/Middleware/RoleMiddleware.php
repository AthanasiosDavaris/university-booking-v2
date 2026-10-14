<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated and has the required role
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required role.'
            ], 403);
        }

        return $next($request);
    }
}
