<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();
        
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
        }

        $guests = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $guests
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'kyc_document_type' => 'nullable|string',
            'kyc_document_number' => 'nullable|string',
        ]);

        // Find or create to prevent duplicates by mobile
        $guest = Guest::firstOrCreate(
            ['mobile_number' => $request->mobile_number],
            [
                'name' => $request->name,
                'email' => $request->email,
                'kyc_document_type' => $request->kyc_document_type,
                'kyc_document_number' => $request->kyc_document_number,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Guest saved successfully',
            'data' => $guest
        ]);
    }

    public function update(Request $request, $id)
    {
        $guest = Guest::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20|unique:guests,mobile_number,'.$id,
            'email' => 'nullable|email',
            'kyc_document_type' => 'nullable|string',
            'kyc_document_number' => 'nullable|string',
        ]);

        $guest->update([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest updated successfully',
            'data' => $guest
        ]);
    }
}
