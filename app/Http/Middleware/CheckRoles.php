<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Check if the user has any of the roles in the array
        foreach ($roles as $role) {
            if ($user->tokenCan('role:' . $role)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Not Authorized'
        ], 401);
    }
}
