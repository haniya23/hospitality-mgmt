<?php

namespace App\Http\Controllers;

use App\Models\B2bPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class B2bController extends Controller
{
    public function index()
    {
        $partners = B2bPartner::with(['contactUser', 'reservedCustomer'])
            ->where('requested_by', auth()->id())
            ->withCount('reservations')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('b2b.index', compact('partners'));
    }

    public function create()
    {
        return view('b2b.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_name' => 'required|string|max:255|unique:b2b_partners,partner_name',
            'partner_type' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20|unique:users,mobile_number',
            'email' => 'nullable|email|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'default_discount_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            // Create user for the partner
            $user = User::create([
                'name' => $validated['contact_person'],
                'mobile_number' => $validated['mobile_number'],
                'email' => $validated['email'],
                'pin_hash' => Hash::make('0000'), // Default PIN
                'is_active' => false, // Inactive until they accept
            ]);

            // Create B2B partner
            $partner = B2bPartner::create([
                'partner_name' => $validated['partner_name'],
                'partner_type' => $validated['partner_type'],
                'contact_user_id' => $user->id,
                'email' => $validated['email'],
                'phone' => $validated['mobile_number'],
                'commission_rate' => $validated['commission_rate'] ?? 10.00,
                'default_discount_pct' => $validated['default_discount_pct'] ?? 5.00,
                'status' => 'active',
                'requested_by' => auth()->id(),
            ]);

            return redirect()->route('b2b.index')
                ->with('success', 'B2B partner created successfully! A reserved customer has been automatically created.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error creating partner: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(B2bPartner $b2b)
    {
        $b2b->load(['contactUser', 'reservedCustomer', 'reservations.guest', 'commissions']);
        
        return view('b2b.show', compact('b2b'));
    }

    public function edit(B2bPartner $b2b)
    {
        $b2b->load('contactUser');
        return view('b2b.edit', compact('b2b'));
    }

    public function update(Request $request, B2bPartner $b2b)
    {
        $validated = $request->validate([
            'partner_name' => 'required|string|max:255|unique:b2b_partners,partner_name,' . $b2b->id,
            'partner_type' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20|unique:users,mobile_number,' . $b2b->contact_user_id,
            'email' => 'nullable|email|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'default_discount_pct' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,inactive,suspended',
        ]);

        try {
            // Update user
            $b2b->contactUser->update([
                'name' => $validated['contact_person'],
                'mobile_number' => $validated['mobile_number'],
                'email' => $validated['email'],
            ]);

            // Update B2B partner
            $b2b->update([
                'partner_name' => $validated['partner_name'],
                'partner_type' => $validated['partner_type'],
                'email' => $validated['email'],
                'phone' => $validated['mobile_number'],
                'commission_rate' => $validated['commission_rate'] ?? $b2b->commission_rate,
                'default_discount_pct' => $validated['default_discount_pct'] ?? $b2b->default_discount_pct,
                'status' => $validated['status'],
            ]);

            return redirect()->route('b2b.index')
                ->with('success', 'B2B partner updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating partner: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(B2bPartner $b2b)
    {
        try {
            // Delete the partner (this will also delete the reserved customer via observer)
            $b2b->delete();

            return redirect()->route('b2b.index')
                ->with('success', 'B2B partner deleted successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error deleting partner: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(B2bPartner $b2b)
    {
        try {
            $newStatus = $b2b->status === 'active' ? 'inactive' : 'active';
            $b2b->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Partner status changed to {$newStatus}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}