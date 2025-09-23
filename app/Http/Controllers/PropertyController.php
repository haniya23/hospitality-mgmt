<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function create()
    {
        $categories = PropertyCategory::all();
        return view('properties.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Check if user can create more properties
        if (!auth()->user()->canCreateProperty()) {
            return back()->withErrors(['error' => 'You have reached your property limit. Please upgrade your plan.'])
                ->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'property_category_id' => 'required|exists:property_categories,id',
            'description' => 'nullable|string',
        ]);

        $property = Property::create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'property_category_id' => $request->property_category_id,
            'description' => $request->description,
            'status' => 'active', // Auto-approve for trial users
            'wizard_step_completed' => 1,
        ]);

        return redirect()->route('dashboard')->with('success', 'Property created successfully!');
    }

    public function index()
    {
        $properties = auth()->user()->properties()->latest()->get();
        return view('properties.index', compact('properties'));
    }

    public function edit(Property $property)
    {
        // Ensure user owns this property
        if ($property->owner_id !== auth()->id()) {
            abort(403);
        }
        
        // Only allow editing of active properties
        if ($property->status !== 'active') {
            return view('properties.pending-approval', compact('property'));
        }
        
        return view('properties.edit', compact('property'));
    }

    public function updateSection(Request $request, Property $property)
    {
        if ($property->owner_id !== auth()->id()) {
            abort(403);
        }

        $section = $request->input('section');
        
        if ($section === 'basic') {
            $request->validate([
                'name' => 'required|string|max:255',
                'property_category_id' => 'required|exists:property_categories,id',
                'description' => 'nullable|string',
            ]);
            
            $property->update($request->only(['name', 'property_category_id', 'description']));
        }
        
        if ($section === 'location') {
            $request->validate([
                'address' => 'required|string',
                'country_id' => 'required|exists:countries,id',
            ]);
            
            $property->location()->updateOrCreate(
                ['property_id' => $property->id],
                $request->only(['address', 'country_id', 'state_id'])
            );
        }
        
        if ($section === 'accommodation') {
            $request->validate([
                'accommodation_name' => 'required|string|max:255',
                'max_occupancy' => 'required|integer|min:1',
                'base_price' => 'required|numeric|min:0',
            ]);
            
            $property->accommodations()->updateOrCreate(
                ['property_id' => $property->id],
                [
                    'name' => $request->accommodation_name,
                    'description' => $request->accommodation_description,
                    'max_occupancy' => $request->max_occupancy,
                    'base_price' => $request->base_price,
                    'status' => 'active',
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Updated successfully!']);
    }
}