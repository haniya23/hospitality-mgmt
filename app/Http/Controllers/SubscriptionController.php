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
        $request->validate([
            'plan' => 'required|in:starter,professional',
            'billing' => 'required|in:monthly,yearly'
        ]);
        
        $user = auth()->user();
        
        // Check if user already has pending request
        if ($user->hasPendingRequest()) {
            return redirect()->back()->with('warning', 'You already have a pending subscription request.');
        }
        

        
        // Create subscription request for admin approval
        $subscriptionRequest = SubscriptionRequest::create([
            'user_id' => $user->id,
            'requested_plan' => $request->plan,
            'billing_cycle' => $request->billing,
            'status' => 'pending',
        ]);
        
        $planName = ucfirst($request->plan);
        $price = $request->plan === 'starter' ? '₹299' : '₹999';
        $period = $request->billing === 'yearly' ? 'year' : 'month';
        
        return redirect()->back()->with('success', "Subscription request for {$planName} plan ({$price}/{$period}) sent successfully! Our executives will contact you soon.");
    }
}