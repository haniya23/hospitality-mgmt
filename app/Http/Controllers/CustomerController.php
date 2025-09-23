<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Guest::withCount('reservations')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email',
            'mobile_number' => 'nullable|string|unique:guests,mobile_number',
            'phone' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'id_type' => 'nullable|string',
            'id_number' => 'required|string'
        ]);

        try {
            Guest::create($request->all());
            
            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Guest $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Guest $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email,' . $customer->id,
            'mobile_number' => 'nullable|string|unique:guests,mobile_number,' . $customer->id,
            'phone' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'id_type' => 'nullable|string',
            'id_number' => 'required|string'
        ]);

        try {
            $customer->update($request->all());
            
            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }
}