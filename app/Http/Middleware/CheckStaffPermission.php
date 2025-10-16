<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  The required permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user || !$user->staffMember) {
            abort(403, 'You do not have staff access.');
        }

        if ($user->staffMember->status !== 'active') {
            abort(403, 'Your staff account is not active.');
        }

        // Check if staff member has the required permission
        if (!$user->staffMember->hasPermission($permission)) {
            abort(403, "You do not have permission to access this resource.");
        }

        return $next($request);
    }
}


