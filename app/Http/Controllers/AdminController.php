<?php

namespace App\Http\Controllers;

use App\Models\Property;
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
}