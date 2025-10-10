<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The required role (manager, supervisor, staff)
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Owners always have access
        if ($user->isOwner()) {
            return $next($request);
        }

        // Check if user has a staff member record
        if (!$user->staffMember) {
            abort(403, 'You do not have staff access.');
        }

        // Check if staff member is active
        if ($user->staffMember->status !== 'active') {
            abort(403, 'Your staff account is not active.');
        }

        // If no specific role required, just check staff membership
        if (!$role) {
            return $next($request);
        }

        // Check specific role requirement
        $hasRequiredRole = match ($role) {
            'manager' => $user->staffMember->isManager(),
            'supervisor' => $user->staffMember->isSupervisor() || $user->staffMember->isManager(),
            'staff' => $user->staffMember->isStaff(),
            'manager_only' => $user->staffMember->isManager(),
            'supervisor_only' => $user->staffMember->isSupervisor(),
            default => false,
        };

        if (!$hasRequiredRole) {
            abort(403, "You need {$role} role to access this resource.");
        }

        return $next($request);
    }
}
