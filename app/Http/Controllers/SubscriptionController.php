<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return view('subscription.plans');
    }

    public function subscribe(Request $request)
    {
        $plan = $request->input('plan');
        $user = auth()->user();
        
        // If user is on trial, update trial plan
        if ($user->subscription_status === 'trial') {
            $properties_limit = $plan === 'professional' ? 5 : 1;
            
            $user->update([
                'trial_plan' => $plan,
                'properties_limit' => $properties_limit,
            ]);
            
            return redirect()->route('dashboard')->with('success', 'Trial plan updated! Enjoy ' . ucfirst($plan) . ' features during your trial.');
        }
        
        // For paid subscriptions, create request for admin approval
        if (!$user->hasPendingRequest()) {
            SubscriptionRequest::create([
                'user_id' => $user->id,
                'requested_plan' => $plan,
                'billing_cycle' => $request->input('billing', 'monthly'),
            ]);
            
            return redirect()->route('dashboard')->with('info', 'Subscription request sent to admin for approval.');
        }
        
        return redirect()->route('dashboard')->with('warning', 'You already have a pending subscription request.');
    }
}