<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next, $feature = null)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if trial expired
        if ($user->isTrialExpired() && $user->subscription_status === 'trial') {
            return redirect()->route('subscription.plans')
                ->with('error', 'Your trial has expired. Please upgrade to continue.');
        }

        // Feature-specific checks
        if ($feature) {
            switch ($feature) {
                case 'b2b':
                    if (($user->subscription_status === 'trial' && $user->trial_plan !== 'professional') || 
                        ($user->subscription_status !== 'trial' && $user->subscription_status !== 'professional')) {
                        return redirect()->route('subscription.plans')
                            ->with('error', 'B2B Partner management requires Professional plan.');
                    }
                    break;
                    
                case 'advanced_reports':
                    if (($user->subscription_status === 'trial' && $user->trial_plan !== 'professional') || 
                        ($user->subscription_status !== 'trial' && $user->subscription_status !== 'professional')) {
                        return redirect()->route('subscription.plans')
                            ->with('error', 'Advanced reports require Professional plan.');
                    }
                    break;
            }
        }

        return $next($request);
    }
}