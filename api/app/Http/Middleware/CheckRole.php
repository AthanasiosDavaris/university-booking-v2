<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Check if user exists, has a role, and if that role's name is in the allowed $roles array
        if (!$user || !$user->role || !in_array($user->role->name, $roles)) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.'
            ], 403);
        }

        return $next($request);
    }
}
