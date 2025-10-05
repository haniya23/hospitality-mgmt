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

        // Check if any of the assigned property owners have expired subscriptions
        $assignedProperties = $user->staffAssignments()
            ->where('status', 'active')
            ->with('property.owner')
            ->get();
        $expiredOwners = [];
        
        foreach ($assignedProperties as $assignment) {
            $owner = $assignment->property->owner;
            
            // Check if owner's subscription has expired
            if ($owner->subscription_status === 'trial' && $owner->isTrialExpired()) {
                $expiredOwners[] = $owner->name;
            } elseif ($owner->subscription_ends_at && $owner->subscription_ends_at->isPast()) {
                $expiredOwners[] = $owner->name;
            }
        }

        // If any owner's subscription has expired, add a session flag for the banner
        if (!empty($expiredOwners)) {
            session(['staff_owner_subscription_expired' => true]);
            session(['staff_expired_owners' => $expiredOwners]);
        } else {
            session()->forget(['staff_owner_subscription_expired', 'staff_expired_owners']);
        }

        return $next($request);
    }
}
