<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Check if user has staff member record
        if (!$user->staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'This account does not have staff access',
            ], 403);
        }

        // Check if staff member is active
        if ($user->staffMember->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your staff account is not active. Please contact your manager.',
            ], 403);
        }

        return $next($request);
    }
}

