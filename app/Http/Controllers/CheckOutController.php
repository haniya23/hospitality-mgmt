<?php

namespace App\Http\Controllers;

use App\Models\CheckOut;
use App\Models\CheckIn;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOutController extends Controller
{
    /**
     * Display the check-out form for a reservation
     */
    public function show(Request $request, $reservationUuid)
    {
        $reservation = Reservation::with(['guest', 'accommodation', 'property', 'checkInRecord'])
            ->where('uuid', $reservationUuid)
            ->firstOrFail();

        // Check if already checked out
        if ($reservation->checkOutRecord) {
            return redirect()->route('checkout.show', $reservation->checkOutRecord->uuid)
                ->with('info', 'Guest has already checked out.');
        }

        // Check if checked in
        if (!$reservation->checkInRecord) {
            return redirect()->back()
                ->with('error', 'Guest must check in before checking out.');
        }

        return view('checkout.form', compact('reservation'));
    }

    /**
     * Store a new check-out record
     */
    public function store(Request $request, $reservationUuid)
    {
        $reservation = Reservation::with('checkInRecord')->where('uuid', $reservationUuid)->firstOrFail();

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
            'staff_id' => Auth::id(),
            'check_in_id' => $reservation->checkInRecord->id,
            'guest_name' => $validated['guest_name'],
            'room_number' => $validated['room_number'],
            'check_out_time' => $validated['check_out_time'],
            'services_used' => $validated['services_used'] ?? [],
            'late_checkout_charges' => $validated['late_checkout_charges'] ?? 0,
            'service_notes' => $validated['service_notes'],
            'final_bill' => $validated['final_bill'],
            'deposit_refund' => $validated['deposit_refund'] ?? 0,
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['payment_notes'],
            'rating' => $validated['rating'],
            'feedback_comments' => $validated['feedback_comments'],
            'guest_signature' => $validated['guest_signature'],
            'staff_signature' => $validated['staff_signature'],
            'status' => 'completed',
            'room_marked_clean' => false,
        ]);

        // Update reservation status
        $reservation->checkOut();

        return redirect()->route('checkout.success', $checkOut->uuid)
            ->with('success', 'Guest checked out successfully!');
    }

    /**
     * Display check-out success page
     */
    public function success($checkOutUuid)
    {
        $checkOut = CheckOut::with(['reservation.guest', 'reservation.accommodation', 'checkIn', 'staff'])
            ->where('uuid', $checkOutUuid)
            ->firstOrFail();

        return view('checkout.success', compact('checkOut'));
    }

    /**
     * Display check-out details
     */
    public function details($checkOutUuid)
    {
        $checkOut = CheckOut::with(['reservation.guest', 'reservation.accommodation', 'checkIn', 'staff'])
            ->where('uuid', $checkOutUuid)
            ->firstOrFail();

        return view('checkout.details', compact('checkOut'));
    }

    /**
     * Mark room as clean
     */
    public function markRoomClean($checkOutUuid)
    {
        $checkOut = CheckOut::where('uuid', $checkOutUuid)->firstOrFail();
        $checkOut->markRoomAsClean();

        return redirect()->back()
            ->with('success', 'Room marked as clean and ready for next guest.');
    }

    /**
     * Display all check-outs
     */
    public function index()
    {
        $user = auth()->user();
        
        $checkOuts = CheckOut::with(['reservation.accommodation.property', 'guest', 'staff', 'checkIn'])
            ->whereHas('reservation.accommodation.property', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->latest()
            ->paginate(15);

        return view('checkout.index', compact('checkOuts'));
    }

    /**
     * Get booking and service details for auto-fetch
     */
    public function getBookingDetails($reservationUuid)
    {
        $reservation = Reservation::with(['guest', 'accommodation.property', 'checkInRecord'])
            ->where('uuid', $reservationUuid)
            ->firstOrFail();

        return response()->json([
            'booking' => [
                'confirmation_number' => $reservation->confirmation_number,
                'check_in_date' => $reservation->check_in_date->format('Y-m-d'),
                'check_out_date' => $reservation->check_out_date->format('Y-m-d'),
                'adults' => $reservation->adults,
                'children' => $reservation->children,
                'total_amount' => $reservation->total_amount,
                'advance_paid' => $reservation->advance_paid,
                'balance_pending' => $reservation->balance_pending,
            ],
            'guest' => [
                'name' => $reservation->guest->name,
                'email' => $reservation->guest->email,
                'mobile_number' => $reservation->guest->mobile_number,
            ],
            'accommodation' => [
                'display_name' => $reservation->accommodation->display_name,
                'room_type' => $reservation->accommodation->room_type,
                'property_name' => $reservation->accommodation->property->name,
            ],
            'check_in' => $reservation->checkInRecord ? [
                'check_in_time' => $reservation->checkInRecord->check_in_time->format('Y-m-d H:i:s'),
                'special_requests' => $reservation->checkInRecord->special_requests,
                'notes' => $reservation->checkInRecord->notes,
            ] : null,
        ]);
    }
}
