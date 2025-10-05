<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Http\Requests\PropertyUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PropertyController extends Controller
{
    use AuthorizesRequests;
    public function create()
    {
        $categories = PropertyCategory::all();
        return view('properties.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Check if user can create more properties
        if (!auth()->user()->canCreateProperty()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You have reached your property limit. Please upgrade your plan.'], 403);
            }
            return back()->withErrors(['error' => 'You have reached your property limit. Please upgrade your plan.'])
                ->withInput();
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'property_category_id' => 'required|exists:property_categories,id',
                'description' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        try {
            $property = Property::create([
                'owner_id' => auth()->id(),
                'name' => $request->name,
                'property_category_id' => $request->property_category_id,
                'description' => $request->description,
                'status' => 'active', // Auto-approve for trial users
                'wizard_step_completed' => 1,
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to create property: ' . $e->getMessage()], 500);
            }
            throw $e;
        }

        // PropertyController store method called
        
        // Always return JSON for API routes
        if ($request->is('api/*') || str_contains($request->path(), 'api/')) {
            return response()->json($property->load('category'));
        }
        
        // Check if request expects JSON
        if ($request->expectsJson()) {
            return response()->json($property->load('category'));
        }
        
        return redirect()->route('dashboard')->with('success', 'Property created successfully!');
    }
    
    public function getAccommodations(Property $property)
    {
        // Ensure user owns this property
        if ($property->owner_id !== auth()->id()) {
            abort(403);
        }
        
        $accommodations = $property->propertyAccommodations()->get();
        return response()->json($accommodations);
    }
    
    public function storeAccommodation(Request $request, Property $property)
    {
        // Ensure user owns this property
        if ($property->owner_id !== auth()->id()) {
            abort(403);
        }
        
        // Check subscription limits for accommodations - trigger only when used EXCEEDS max
        $user = auth()->user();
        $usage = $user->getUsagePercentage();
        
        if (isset($usage['accommodations']) && 
            $usage['accommodations']['used'] > $usage['accommodations']['max']) {
            
            return response()->json([
                'success' => false,
                'message' => 'You have reached the maximum number of accommodations allowed on your current plan. Please contact WhatsApp support at +91 9400960223 to upgrade your subscription.',
                'error_code' => 'SUBSCRIPTION_LIMIT_EXCEEDED',
                'whatsapp_url' => 'https://wa.me/919400960223?text=Hi%2C%20I%20would%20like%20to%20upgrade%20my%20Stay%20Loops%20subscription%20to%20add%20more%20accommodations.'
            ], 403);
        }
        
        $request->validate([
            'custom_name' => 'required|string|max:255',
            'predefined_type_id' => 'nullable|exists:predefined_accommodation_types,id',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $accommodation = $property->propertyAccommodations()->create([
            'predefined_accommodation_type_id' => $request->predefined_type_id ?: 3, // Default to "Custom" type (ID: 3)
            'custom_name' => $request->custom_name,
            'max_occupancy' => $request->max_occupancy,
            'base_price' => $request->base_price,
            'size' => $request->size,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
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
        
        return response()->json([
            'success' => true,
            'message' => 'Accommodation created successfully!',
            'accommodation' => $accommodation->load(['amenities', 'photos'])
        ]);
    }

    public function index()
    {
        // Get active (non-deleted) properties
        $activeProperties = auth()->user()->properties()
            ->with([
                'category', 
                'location.city.district.state.country',
                'propertyAccommodations',
                'pendingDeleteRequest'
            ])
            ->withCount(['propertyAccommodations', 'reservations as bookings_count'])
            ->latest()
            ->get();
            
        // Get deleted (archived) properties
        $archivedProperties = auth()->user()->properties()
            ->onlyTrashed()
            ->with([
                'category', 
                'location.city.district.state.country',
                'propertyAccommodations'
            ])
            ->withCount(['propertyAccommodations', 'reservations as bookings_count'])
            ->latest('deleted_at')
            ->get();
        
        // All properties for backward compatibility
        $properties = $activeProperties;
        
        $hasB2bPartners = \App\Models\B2bPartner::where('status', 'active')
            ->where('requested_by', auth()->id())
            ->exists();
            
        return view('properties.index', compact('properties', 'activeProperties', 'archivedProperties', 'hasB2bPartners'));
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

    public function updateSection(PropertyUpdateRequest $request, Property $property)
    {
        $section = $request->input('section');
        
        // Property update request
        
        if ($section === 'basic') {
            $updateData = $request->only(['name', 'property_category_id', 'description']);
            $result = $property->update($updateData);
            
            // Update owner name if provided
            if ($request->filled('owner_name')) {
                $ownerResult = $property->owner()->update(['name' => $request->owner_name]);
                // Owner updated
            }
        }
        
        if ($section === 'location') {
            $locationData = $request->only(['address', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id', 'latitude', 'longitude']);
            
            // Ensure address is not null - provide empty string if null
            if (is_null($locationData['address'])) {
                $locationData['address'] = '';
            }
            
            // Filter out null values for optional fields (but keep address as it's required)
            $filteredData = [];
            foreach ($locationData as $key => $value) {
                if ($key === 'address' || !is_null($value)) {
                    $filteredData[$key] = $value;
                }
            }
            
            $result = $property->location()->updateOrCreate(
                ['property_id' => $property->id],
                $filteredData
            );
        }
        
        if ($section === 'accommodation') {
            $request->validate([
                'display_name' => 'required|string|max:255',
                'max_occupancy' => 'required|integer|min:1',
                'base_price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'accommodation_id' => 'nullable|exists:property_accommodations,id'
            ]);

            $payload = [
                'custom_name' => $request->input('display_name'),
                'description' => $request->input('description'),
                'max_occupancy' => $request->input('max_occupancy'),
                'base_price' => $request->input('base_price'),
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->filled('accommodation_id')) {
                $accommodation = $property->propertyAccommodations()->where('id', $request->accommodation_id)->firstOrFail();
                $accommodation->update($payload);
            } else {
                $property->propertyAccommodations()->create($payload);
            }
        }
        
        if ($section === 'amenities') {
            $amenityIds = $request->input('amenities', []);
            $result = $property->amenities()->sync($amenityIds);
        }
        
        if ($section === 'policies') {
            $policyData = $request->only(['check_in_time', 'check_out_time', 'cancellation_policy', 'house_rules']);
            $result = $property->policy()->updateOrCreate(
                ['property_id' => $property->id],
                $policyData
            );
        }

        return response()->json([
            'success' => true, 
            'message' => 'Updated successfully!',
            'section' => $section,
            'property_id' => $property->id
        ]);
    }

    /**
     * Test endpoint to verify AJAX data reception
     */
    public function testAjax(PropertyUpdateRequest $request, Property $property)
    {
        // Test AJAX endpoint called

        return response()->json([
            'success' => true,
            'message' => 'Test endpoint reached',
            'received_data' => $request->all(),
            'section' => $request->input('section'),
            'property_id' => $property->id
        ]);
    }


    public function editSection(Property $property, Request $request)
    {
        $this->authorize('update', $property);
        
        $section = $request->input('section', 'basic');
        
        // Load necessary data based on section
        $data = [
            'property' => $property,
            'section' => $section
        ];
        
        switch ($section) {
            case 'basic':
                $data['categories'] = \App\Models\PropertyCategory::all();
                break;
            case 'location':
                $data['countries'] = \App\Models\Country::all();
                $data['states'] = \App\Models\State::all();
                $data['districts'] = \App\Models\District::all();
                $data['cities'] = \App\Models\City::all();
                $data['pincodes'] = \App\Models\Pincode::all();
                break;
            case 'amenities':
                $data['amenities'] = \App\Models\Amenity::all();
                break;
            case 'policies':
                // No additional data needed for policies
                break;
        }
        
        return view('properties.partials.modal-content', $data);
    }

    public function createAccommodation(Property $property)
    {
        $this->authorize('update', $property);
        
        $predefinedTypes = \App\Models\PredefinedAccommodationType::all();
        
        return view('properties.partials.accommodation-form', [
            'property' => $property,
            'accommodation' => null,
            'predefinedTypes' => $predefinedTypes,
            'isEdit' => false
        ]);
    }

    public function editAccommodation(Property $property, $accommodationId)
    {
        $this->authorize('update', $property);
        
        $accommodation = $property->propertyAccommodations()->with(['amenities', 'photos'])->findOrFail($accommodationId);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'accommodation' => $accommodation
            ]);
        }
        
        $predefinedTypes = \App\Models\PredefinedAccommodationType::all();
        
        return view('properties.partials.accommodation-form', [
            'property' => $property,
            'accommodation' => $accommodation,
            'predefinedTypes' => $predefinedTypes,
            'isEdit' => true
        ]);
    }


    public function updateAccommodation(Request $request, Property $property, $accommodationId)
    {
        $this->authorize('updateAccommodation', $property);
        
        $accommodation = $property->propertyAccommodations()->findOrFail($accommodationId);
        
        $request->validate([
            'predefined_type_id' => 'nullable|exists:predefined_accommodation_types,id',
            'custom_name' => 'required|string|max:255',
            'max_occupancy' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $accommodation->update([
            'predefined_accommodation_type_id' => $request->predefined_type_id ?: 3, // Default to "Custom" type (ID: 3)
            'custom_name' => $request->custom_name,
            'max_occupancy' => $request->max_occupancy,
            'base_price' => $request->base_price,
            'size' => $request->size,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
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

        return response()->json([
            'success' => true, 
            'message' => 'Accommodation updated successfully!',
            'accommodation' => $accommodation->load(['amenities', 'photos'])
        ]);
    }

    public function deleteAccommodation(Property $property, $accommodationId)
    {
        $this->authorize('deleteAccommodation', $property);
        
        $accommodation = $property->propertyAccommodations()->findOrFail($accommodationId);
        $accommodation->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Accommodation deleted successfully!'
        ]);
    }

    public function storePhotos(Request $request, Property $property)
    {
        $this->authorize('update', $property);
        
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'main_photo_id' => 'nullable|exists:property_photos,id',
            'main_photo_new' => 'nullable|integer',
        ]);

        // Handle new photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('property-photos', 'public');
                
                $isMain = false;
                if ($request->filled('main_photo_new') && $request->main_photo_new == $index) {
                    $isMain = true;
                }
                
                $property->photos()->create([
                    'file_path' => $path,
                    'is_main' => $isMain,
                ]);
            }
        }

        // Handle setting main photo from existing photos
        if ($request->filled('main_photo_id')) {
            // Remove main flag from all photos
            $property->photos()->update(['is_main' => false]);
            // Set new main photo
            $property->photos()->where('id', $request->main_photo_id)->update(['is_main' => true]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Photos updated successfully!'
        ]);
    }

    public function deletePhoto(Property $property, $photoId)
    {
        $this->authorize('update', $property);
        
        $photo = $property->photos()->findOrFail($photoId);
        
        // Delete file from storage
        if (\Storage::disk('public')->exists($photo->file_path)) {
            \Storage::disk('public')->delete($photo->file_path);
        }
        
        $photo->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Photo deleted successfully!'
        ]);
    }
}