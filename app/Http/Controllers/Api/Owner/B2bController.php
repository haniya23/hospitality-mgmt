<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\B2bPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class B2bController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->b2bPartners()
            ->with('reservedCustomer')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('partner_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $partners = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $partners
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'partner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $partner = $request->user()->b2bPartners()->create([
            'uuid' => Str::uuid(),
            'partner_name' => $request->partner_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'commission_rate' => $request->commission_rate ?? 10.0,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Partner added successfully',
            'data' => $partner
        ]);
    }

    public function update(Request $request, $id)
    {
        $partner = $request->user()->b2bPartners()->findOrFail($id);

        $request->validate([
            'partner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $partner->update([
            'partner_name' => $request->partner_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'commission_rate' => $request->commission_rate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Partner updated successfully',
            'data' => $partner
        ]);
    }
}
