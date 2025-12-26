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
}
