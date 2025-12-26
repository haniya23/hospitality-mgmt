<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    /**
     * Store a new check-in record
     */
    public function store(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check if already checked in
        if ($reservation->checkInRecord) {
            return response()->json(['message' => 'Guest has already checked in.'], 400);
        }

        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_contact' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'guest_address' => 'nullable|string|max:1000',
            'id_proof_type' => 'nullable|string|in:passport,aadhaar,driving_license,pan,voter_id',
            'id_proof_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'check_in_time' => 'required|date',
            'expected_check_out_date' => 'required|date|after_or_equal:today',
            'special_requests' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'guest_signature' => 'nullable|string',
            'staff_signature' => 'nullable|string',
        ]);

        // Create check-in record
        $checkIn = CheckIn::create([
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'staff_id' => Auth::id() ?? $reservation->user_id, // Fallback to owner if API auth weirdness
            'guest_name' => $validated['guest_name'],
            'guest_contact' => $validated['guest_contact'],
            'guest_email' => $validated['guest_email'],
            'guest_address' => $validated['guest_address'],
            'id_proof_type' => $validated['id_proof_type'],
            'id_proof_number' => $validated['id_proof_number'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'check_in_time' => $validated['check_in_time'],
            'expected_check_out_date' => $validated['expected_check_out_date'],
            'special_requests' => $validated['special_requests'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'guest_signature' => $validated['guest_signature'] ?? null,
            'staff_signature' => $validated['staff_signature'] ?? null,
            'status' => 'completed',
        ]);

        // Update reservation status
        $reservation->checkIn();

        return response()->json([
            'success' => true,
            'message' => 'Guest checked in successfully',
            'data' => $checkIn
        ]);
    }
    /**
     * List recent check-ins
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $checkIns = CheckIn::with(['reservation.accommodation.property', 'guest'])
            ->whereHas('reservation.accommodation.property', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $checkIns
        ]);
    }

    /**
     * Get check-in details
     */
    public function show(Request $request, $uuid)
    {
        $checkIn = CheckIn::with(['reservation.accommodation.property', 'reservation.guest', 'guest', 'staff'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Ensure owner owns the property
        if ($checkIn->reservation->accommodation->property->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $checkIn
        ]);
    }
}
