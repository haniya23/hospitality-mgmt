<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('staff.login');
        }

        $user = auth()->user();
        
        // Check if user is staff
        if (!$user->isStaff()) {
            return redirect()->route('login')->with('error', 'Staff access required.');
        }

        // Check if staff has active assignments
        if (!$user->getActiveStaffAssignments()->count()) {
            Auth::logout();
            return redirect()->route('staff.login')->with('error', 'No active staff assignments found.');
        }

        return $next($request);
    }
}
