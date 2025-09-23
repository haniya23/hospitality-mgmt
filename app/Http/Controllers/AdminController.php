<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SubscriptionRequest;
use App\Models\User;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $stats = [
            'pending_properties' => Property::where('status', 'pending')->count(),
            'total_users' => User::where('is_admin', false)->count(),
            'total_properties' => Property::count(),
            'pending_subscriptions' => SubscriptionRequest::where('status', 'pending')->count(),
            'total_customers' => Guest::count(),
            'b2b_partners' => B2bPartner::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    // Property Approval Section
    public function propertyApprovals()
    {
        $this->checkAdmin();
        $pendingProperties = Property::where('status', 'pending')
            ->with(['owner', 'category', 'location'])
            ->latest()
            ->paginate(10);
        return view('admin.property-approvals', compact('pendingProperties'));
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

    public function rejectProperty(Property $property, Request $request)
    {
        $this->checkAdmin();
        $property->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason')
        ]);
        return back()->with('success', 'Property rejected.');
    }

    // Subscription Management
    public function subscriptions()
    {
        $this->checkAdmin();
        $subscriptionRequests = SubscriptionRequest::with('user')
            ->latest()
            ->paginate(15);
        $activeSubscriptions = User::where('subscription_status', '!=', 'trial')
            ->where('is_admin', false)
            ->with('properties')
            ->paginate(15);
        return view('admin.subscriptions', compact('subscriptionRequests', 'activeSubscriptions'));
    }

    public function approveSubscription(SubscriptionRequest $request, Request $httpRequest)
    {
        $this->checkAdmin();
        $months = $httpRequest->input('months', 1);
        $user = $request->user;
        $duration = $request->billing_cycle === 'yearly' ? 12 : $months;
        
        $user->update([
            'subscription_status' => $request->requested_plan,
            'properties_limit' => $request->requested_plan === 'professional' ? 5 : 3,
            'subscription_ends_at' => now()->addMonths($duration),
            'is_trial_active' => false,
        ]);
        
        $request->update([
            'status' => 'approved',
            'admin_notes' => 'Approved for ' . $duration . ' months (' . $request->billing_cycle . ')'
        ]);
        
        return back()->with('success', 'Subscription approved successfully!');
    }

    public function updateSubscriptionStatus(User $user, Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'subscription_status' => 'required|in:trial,starter,professional',
            'subscription_ends_at' => 'nullable|date',
            'properties_limit' => 'required|integer|min:1|max:10'
        ]);

        $user->update([
            'subscription_status' => $request->subscription_status,
            'subscription_ends_at' => $request->subscription_ends_at,
            'properties_limit' => $request->properties_limit,
            'is_trial_active' => $request->subscription_status === 'trial'
        ]);

        return back()->with('success', 'Subscription updated successfully!');
    }

    // User Management
    public function userManagement()
    {
        $this->checkAdmin();
        $users = User::where('is_admin', false)
            ->withCount('properties')
            ->latest()
            ->paginate(15);
        return view('admin.user-management', compact('users'));
    }

    public function createUser()
    {
        $this->checkAdmin();
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:users,mobile_number|max:15',
            'email' => 'nullable|email|unique:users,email',
            'pin' => 'required|string|min:4|max:6',
            'subscription_status' => 'required|in:trial,starter,professional',
            'properties_limit' => 'required|integer|min:1|max:10'
        ]);

        User::create([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'pin_hash' => Hash::make($request->pin),
            'subscription_status' => $request->subscription_status,
            'properties_limit' => $request->properties_limit,
            'is_trial_active' => $request->subscription_status === 'trial',
            'subscription_ends_at' => $request->subscription_status !== 'trial' ? now()->addYear() : null,
        ]);

        return redirect()->route('admin.user-management')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        $this->checkAdmin();
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(User $user, Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:users,mobile_number,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'subscription_status' => 'required|in:trial,starter,professional',
            'properties_limit' => 'required|integer|min:1|max:10'
        ]);

        $user->update([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'subscription_status' => $request->subscription_status,
            'properties_limit' => $request->properties_limit,
            'is_trial_active' => $request->subscription_status === 'trial',
        ]);

        return redirect()->route('admin.user-management')->with('success', 'User updated successfully!');
    }

    // Customer Data Management
    public function customerData()
    {
        $this->checkAdmin();
        $customers = Guest::with(['reservations.property'])
            ->withCount('reservations')
            ->latest()
            ->paginate(15);
        return view('admin.customer-data', compact('customers'));
    }

    // B2B Management
    public function b2bManagement()
    {
        $this->checkAdmin();
        $b2bPartners = B2bPartner::with(['contactUser', 'requests'])
            ->withCount('requests')
            ->latest()
            ->paginate(15);
        return view('admin.b2b-management', compact('b2bPartners'));
    }

    // Property Location Analytics
    public function locationAnalytics()
    {
        $this->checkAdmin();
        
        // Get properties with location data
        $properties = Property::with(['location.city.district.state.country'])
            ->whereHas('location')
            ->get();
            
        $locationStats = collect();
        
        // Group by location and calculate stats
        $grouped = $properties->groupBy(function ($property) {
            if ($property->location && $property->location->city) {
                return $property->location->city->name . ', ' . 
                       $property->location->city->district->name . ', ' . 
                       $property->location->city->district->state->name;
            }
            return 'Unknown Location';
        });
        
        foreach ($grouped as $location => $props) {
            $totalCount = $props->count();
            $activeCount = $props->where('status', 'active')->count();
            $approvalRate = $totalCount > 0 ? round(($activeCount / $totalCount) * 100, 1) : 0;
            
            $locationStats->push([
                'location' => $location,
                'property_count' => $totalCount,
                'approval_rate' => $approvalRate
            ]);
        }
        
        // Sort by property count descending
        $locationStats = $locationStats->sortByDesc('property_count');
            
        return view('admin.location-analytics', compact('locationStats'));
    }

    // Admin Property Creation
    public function createPropertyForUser()
    {
        $this->checkAdmin();
        $users = User::where('is_admin', false)->get();
        $categories = PropertyCategory::all();
        return view('admin.create-property', compact('users', 'categories'));
    }

    public function storePropertyForUser(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'owner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:property_categories,id',
            'description' => 'nullable|string'
        ]);

        Property::create([
            'owner_id' => $request->owner_id,
            'name' => $request->name,
            'property_category_id' => $request->category_id,
            'description' => $request->description,
            'status' => 'active', // Admin-created properties are auto-approved
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.property-approvals')->with('success', 'Property created and assigned successfully!');
    }
}