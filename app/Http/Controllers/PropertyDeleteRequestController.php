<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyDeleteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyDeleteRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($request->property_id);
        
        // Ensure user owns this property
        if ($property->owner_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only request deletion of your own properties.'
            ], 403);
        }

        // Check if there's already a pending request for this property
        $existingRequest = PropertyDeleteRequest::where('property_id', $property->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'A delete request for this property is already pending.'
            ], 409);
        }

        // Create the delete request
        $deleteRequest = PropertyDeleteRequest::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Delete request submitted successfully. An admin will review your request.',
            'request_id' => $deleteRequest->uuid,
        ]);
    }

    public function index()
    {
        $deleteRequests = PropertyDeleteRequest::with(['property', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('requested_at', 'desc')
            ->get();

        return response()->json($deleteRequests);
    }

    public function show(PropertyDeleteRequest $deleteRequest)
    {
        // Ensure user owns this request
        if ($deleteRequest->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only view your own delete requests.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $deleteRequest->load(['property', 'user', 'processedBy'])
        ]);
    }

    public function cancel(PropertyDeleteRequest $deleteRequest)
    {
        // Ensure user owns this request
        if ($deleteRequest->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only cancel your own delete requests.'
            ], 403);
        }

        // Can only cancel pending requests
        if (!$deleteRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be cancelled.'
            ], 400);
        }

        $deleteRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delete request cancelled successfully.'
        ]);
    }
}