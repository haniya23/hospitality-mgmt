<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\PredefinedAccommodationType;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AccommodationController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = PropertyAccommodation::with(['property', 'predefinedType', 'amenities', 'photos'])
            ->whereHas('property', function($q) {
                $q->where('owner_id', auth()->id());
            });

        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('custom_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('property', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $accommodations = $query->latest()->paginate(12);
        $properties = Property::where('owner_id', auth()->id())->get(['id', 'name']);

        return view('accommodations.index', compact('accommodations', 'properties'));
    }

    public function create()
    {
        $properties = Property::where('owner_id', auth()->id())->get(['id', 'name']);
        $predefinedTypes = PredefinedAccommodationType::all(['id', 'name']);
        $amenities = Amenity::all(['id', 'name', 'icon']);

        return view('accommodations.create', compact('properties', 'predefinedTypes', 'amenities'));
    }

    public function store(Request $request)
    {
        // Check subscription limits for accommodations - trigger only when used EXCEEDS max
        $user = auth()->user();
        $usage = $user->getUsagePercentage();
        
        if (isset($usage['accommodations']) && 
            $usage['accommodations']['used'] > $usage['accommodations']['max']) {
            
            return back()->withErrors([
                'subscription_limit' => 'You have reached the maximum number of accommodations allowed on your current plan. Please contact WhatsApp support at +91 9400960223 to upgrade your subscription.'
            ])->withInput();
        }
        
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'custom_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'predefined_type_id' => 'nullable|exists:predefined_accommodation_types,id',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $accommodation = PropertyAccommodation::create([
            'property_id' => $request->property_id,
            'custom_name' => $request->custom_name,
            'description' => $request->description,
            'predefined_type_id' => $request->predefined_type_id,
            'base_price' => $request->base_price,
            'max_occupancy' => $request->max_occupancy,
            'size' => $request->size,
        ]);

        // Attach amenities
        if ($request->amenities) {
            $accommodation->amenities()->attach($request->amenities);
        }

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('accommodations', 'public');
                $accommodation->photos()->create([
                    'file_path' => $path,
                    'is_main' => false
                ]);
            }
        }

        return redirect()->route('accommodations.index')
            ->with('success', 'Accommodation created successfully.');
    }

    public function show(PropertyAccommodation $accommodation)
    {
        // Check if user owns the property
        if ($accommodation->property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this accommodation.');
        }
        
        $accommodation->load(['property', 'predefinedType', 'amenities', 'photos']);
        
        return view('accommodations.show', compact('accommodation'));
    }

    public function edit(PropertyAccommodation $accommodation)
    {
        // Check if user owns the property
        if ($accommodation->property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this accommodation.');
        }
        
        $properties = Property::where('owner_id', auth()->id())->get(['id', 'name']);
        $predefinedTypes = PredefinedAccommodationType::all(['id', 'name']);
        $amenities = Amenity::all(['id', 'name', 'icon']);
        
        $accommodation->load(['amenities', 'photos']);
        
        return view('accommodations.edit', compact('accommodation', 'properties', 'predefinedTypes', 'amenities'));
    }

    public function update(Request $request, PropertyAccommodation $accommodation)
    {
        // Check if user owns the property
        if ($accommodation->property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this accommodation.');
        }
        
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'custom_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'predefined_type_id' => 'nullable|exists:predefined_accommodation_types,id',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $accommodation->update([
            'property_id' => $request->property_id,
            'custom_name' => $request->custom_name,
            'description' => $request->description,
            'predefined_type_id' => $request->predefined_type_id,
            'base_price' => $request->base_price,
            'max_occupancy' => $request->max_occupancy,
            'size' => $request->size,
        ]);

        // Sync amenities
        $accommodation->amenities()->sync($request->amenities ?? []);

        // Handle new photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('accommodations', 'public');
                $accommodation->photos()->create([
                    'file_path' => $path,
                    'is_main' => false
                ]);
            }
        }

        return redirect()->route('accommodations.index')
            ->with('success', 'Accommodation updated successfully.');
    }

    public function destroy(PropertyAccommodation $accommodation)
    {
        // Check if user owns the property
        if ($accommodation->property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this accommodation.');
        }
        
        // Delete photos from storage
        foreach ($accommodation->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }
        
        $accommodation->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Accommodation deleted successfully.']);
        }
        
        return redirect()->route('accommodations.index')
            ->with('success', 'Accommodation deleted successfully.');
    }
}
