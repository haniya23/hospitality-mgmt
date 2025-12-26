<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Get owner properties list
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $properties = $user->properties()
            ->with([
                'category', 
                'location.city.district.state', 
                'photos',
                'amenities',
                'propertyAccommodations.reservedCustomer', 
                'propertyAccommodations.predefinedType',
                'propertyAccommodations.photos',
                'propertyAccommodations.amenities'
            ])
            ->withCount('propertyAccommodations')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties
        ]);
    }
    /**
     * Update property details
     */
    public function update(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $property->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully',
            'data' => $property
        ]);
    }

    /**
     * Toggle property status
     */
    public function toggleStatus(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => 'Property status updated to ' . $newStatus,
            'data' => $property
        ]);
    }
}
