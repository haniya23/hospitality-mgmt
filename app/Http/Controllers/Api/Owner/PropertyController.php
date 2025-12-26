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
    /**
     * Store property photos
     */
    public function storePhotos(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('property-photos', 'public');
                $property->photos()->create([
                    'file_path' => $path,
                    'is_main' => $property->photos()->doesntExist(), // First photo is main
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Photos uploaded successfully',
            'data' => $property->load('photos')
        ]);
    }
    
    /**
     * Delete property photo
     */
    public function deletePhoto(Request $request, $id, $photoId)
    {
        $property = $request->user()->properties()->findOrFail($id);
        $photo = $property->photos()->where('id', $photoId)->firstOrFail();
        
        // Delete from storage
        if (\Storage::disk('public')->exists($photo->file_path)) {
            \Storage::disk('public')->delete($photo->file_path);
        }
        
        $photo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully'
        ]);
    }

    /**
     * Store accommodation photos
     */
    public function storeAccommodationPhotos(Request $request, $id, $accommodationId)
    {
        $property = $request->user()->properties()->findOrFail($id);
        $accommodation = $property->propertyAccommodations()->findOrFail($accommodationId);
        
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('accommodations', 'public');
                $accommodation->photos()->create([
                    'file_path' => $path,
                    'is_main' => $accommodation->photos()->doesntExist(),
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Photos uploaded successfully',
            'data' => $accommodation->load('photos')
        ]);
    }
    
    /**
     * Delete accommodation photo
     */
    public function deleteAccommodationPhoto(Request $request, $id, $accommodationId, $photoId)
    {
        $property = $request->user()->properties()->findOrFail($id);
        $accommodation = $property->propertyAccommodations()->findOrFail($accommodationId);
        $photo = $accommodation->photos()->where('id', $photoId)->firstOrFail();
        
        if (\Storage::disk('public')->exists($photo->file_path)) {
            \Storage::disk('public')->delete($photo->file_path);
        }
        
        $photo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully'
        ]);
    }
}
