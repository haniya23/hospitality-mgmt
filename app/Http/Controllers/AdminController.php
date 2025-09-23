<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Admin access required');
        }
    }

    public function dashboard()
    {
        $this->checkAdmin();
        $pendingProperties = Property::where('status', 'pending')->with('owner', 'category')->latest()->get();
        return view('admin.dashboard', compact('pendingProperties'));
    }

    public function approveProperty(Property $property)
    {
        $this->checkAdmin();
        $property->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Property approved successfully!');
    }

    public function rejectProperty(Property $property)
    {
        $this->checkAdmin();
        $property->update(['status' => 'rejected']);
        return back()->with('success', 'Property rejected.');
    }

    public function subscriptions()
    {
        $this->checkAdmin();
        $pendingRequests = SubscriptionRequest::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
        return view('admin.subscriptions', compact('pendingRequests'));
    }

    public function approveSubscription(SubscriptionRequest $request, Request $httpRequest)
    {
        $this->checkAdmin();
        
        $months = $httpRequest->input('months', 1);
        $user = $request->user;
        
        // Calculate subscription duration based on billing cycle
        $duration = $request->billing_cycle === 'yearly' ? 12 : $months;
        
        $user->update([
            'subscription_status' => $request->requested_plan,
            'properties_limit' => $request->requested_plan === 'professional' ? 5 : 1,
            'subscription_ends_at' => now()->addMonths($duration),
            'is_trial_active' => false,
        ]);
        
        $request->update([
            'status' => 'approved',
            'admin_notes' => 'Approved for ' . $duration . ' months (' . $request->billing_cycle . ')'
        ]);
        
        return back()->with('success', 'Subscription approved successfully!');
    }
}