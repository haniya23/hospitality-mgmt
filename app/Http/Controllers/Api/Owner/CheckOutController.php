<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\CheckOut;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOutController extends Controller
{
    /**
     * Store a new check-out record
     */
    public function store(Request $request, $id)
    {
        $reservation = Reservation::with('checkInRecord')->findOrFail($id);

        // Check if already checked out
        if ($reservation->checkOutRecord) {
             return response()->json(['message' => 'Guest has already checked out.'], 400);
        }

        // Check if checked in
        if (!$reservation->checkInRecord) {
             return response()->json(['message' => 'Guest must check in before checking out.'], 400);
        }

        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'room_number' => 'nullable|string|max:50',
            'check_out_time' => 'required|date',
            'services_used' => 'nullable|array',
            'services_used.*' => 'string|max:100',
            'late_checkout_charges' => 'nullable|numeric|min:0',
            'service_notes' => 'nullable|string|max:1000',
            'final_bill' => 'required|numeric|min:0',
            'deposit_refund' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,completed,partial,refunded',
            'payment_notes' => 'nullable|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback_comments' => 'nullable|string|max:1000',
            'guest_signature' => 'nullable|string',
            'staff_signature' => 'nullable|string',
        ]);

        // Create check-out record
        $checkOut = CheckOut::create([
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'staff_id' => Auth::id() ?? $reservation->user_id,
            'check_in_id' => $reservation->checkInRecord->id,
            'guest_name' => $validated['guest_name'],
            'room_number' => $validated['room_number'],
            'check_out_time' => $validated['check_out_time'],
            'services_used' => $validated['services_used'] ?? [],
            'late_checkout_charges' => $validated['late_checkout_charges'] ?? 0,
            'service_notes' => $validated['service_notes'] ?? null,
            'final_bill' => $validated['final_bill'],
            'deposit_refund' => $validated['deposit_refund'] ?? 0,
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['payment_notes'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'feedback_comments' => $validated['feedback_comments'] ?? null,
            'guest_signature' => $validated['guest_signature'] ?? null,
            'staff_signature' => $validated['staff_signature'] ?? null,
            'status' => 'completed',
            'room_marked_clean' => false,
        ]);

        // Update reservation status
        $reservation->checkOut();

        return response()->json([
            'success' => true,
            'message' => 'Guest checked out successfully',
            'data' => $checkOut
        ]);
    }
}
