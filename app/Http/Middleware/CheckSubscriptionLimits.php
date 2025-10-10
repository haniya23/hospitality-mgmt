<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        // Skip subscription limit checks for admin users
        if ($user->is_admin) {
            return $next($request);
        }

        // Get user's current usage
        $usage = $user->getUsagePercentage();
        
        // Check accommodation limits - trigger only when used EXCEEDS max (not equal)
        if (isset($usage['accommodations']) && 
            $usage['accommodations']['used'] > $usage['accommodations']['max']) {
            
            // Allow access to subscription-related pages
            $allowedRoutes = [
                'subscription.plans',
                'subscription.upgrade',
                'logout',
                'subscription.limit-exceeded',
                'cashfree.create-order', // Allow payment processing
                'cashfree.success' // Allow payment success page
            ];
            
            $currentRoute = $request->route()?->getName();
            
            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('subscription.limit-exceeded');
            }
        }

        return $next($request);
    }
}
