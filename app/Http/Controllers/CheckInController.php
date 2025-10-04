<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Reservation;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    /**
     * Display the check-in form for a reservation
     */
    public function show(Request $request, $reservationUuid)
    {
        $reservation = Reservation::with(['guest', 'accommodation', 'property'])
            ->where('uuid', $reservationUuid)
            ->firstOrFail();

        // Check if already checked in
        if ($reservation->checkInRecord) {
            return redirect()->route('checkin.show', $reservation->checkInRecord->uuid)
                ->with('info', 'Guest has already checked in.');
        }

        return view('checkin.form', compact('reservation'));
    }

    /**
     * Store a new check-in record
     */
    public function store(Request $request, $reservationUuid)
    {
        $reservation = Reservation::where('uuid', $reservationUuid)->firstOrFail();

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
            'staff_id' => Auth::id(),
            'guest_name' => $validated['guest_name'],
            'guest_contact' => $validated['guest_contact'],
            'guest_email' => $validated['guest_email'],
            'guest_address' => $validated['guest_address'],
            'id_proof_type' => $validated['id_proof_type'],
            'id_proof_number' => $validated['id_proof_number'],
            'nationality' => $validated['nationality'],
            'check_in_time' => $validated['check_in_time'],
            'expected_check_out_date' => $validated['expected_check_out_date'],
            'special_requests' => $validated['special_requests'],
            'notes' => $validated['notes'],
            'guest_signature' => $validated['guest_signature'],
            'staff_signature' => $validated['staff_signature'],
            'status' => 'completed',
        ]);

        // Update reservation status
        $reservation->checkIn();

        return redirect()->route('checkin.success', $checkIn->uuid)
            ->with('success', 'Guest checked in successfully!');
    }

    /**
     * Display check-in success page
     */
    public function success($checkInUuid)
    {
        $checkIn = CheckIn::with(['reservation.guest', 'reservation.accommodation', 'staff'])
            ->where('uuid', $checkInUuid)
            ->firstOrFail();

        return view('checkin.success', compact('checkIn'));
    }

    /**
     * Display check-in details
     */
    public function details($checkInUuid)
    {
        $checkIn = CheckIn::with(['reservation.guest', 'reservation.accommodation', 'staff'])
            ->where('uuid', $checkInUuid)
            ->firstOrFail();

        return view('checkin.details', compact('checkIn'));
    }

    /**
     * Display confirmed bookings ready for check-in
     */
    public function confirmedBookings()
    {
        $user = auth()->user();
        
        // Show confirmed bookings for properties owned by the current user
        $confirmedBookings = Reservation::with(['guest', 'accommodation.property', 'checkInRecord'])
            ->whereHas('accommodation.property', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->where('status', 'confirmed')
            ->orderBy('check_in_date')
            ->get()
            ->groupBy('accommodation.property.name');

        // Debug: Log the results
        \Log::info('Confirmed bookings query result:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'total_bookings' => $confirmedBookings->flatten()->count(),
            'properties' => $confirmedBookings->keys()->toArray()
        ]);

        return view('checkin.confirmed-bookings', compact('confirmedBookings'));
    }

    /**
     * Display all check-ins
     */
    public function index()
    {
        $user = auth()->user();
        
        $checkIns = CheckIn::with(['reservation.accommodation.property', 'guest', 'staff'])
            ->whereHas('reservation.accommodation.property', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->latest()
            ->paginate(15);

        return view('checkin.index', compact('checkIns'));
    }

    /**
     * Update customer details in booking
     */
    public function updateCustomerDetails(Request $request, $reservationUuid)
    {
        $reservation = Reservation::with('guest')->where('uuid', $reservationUuid)->firstOrFail();

        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_mobile' => 'required|string|max:20',
            'guest_address' => 'nullable|string|max:1000',
            'id_type' => 'nullable|string|in:passport,aadhaar,driving_license,pan,voter_id',
            'id_number' => 'nullable|string|max:50',
        ]);

        // Update guest details
        $reservation->guest->update([
            'name' => $validated['guest_name'],
            'email' => $validated['guest_email'],
            'mobile_number' => $validated['guest_mobile'],
            'address' => $validated['guest_address'],
            'id_type' => $validated['id_type'],
            'id_number' => $validated['id_number'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer details updated successfully',
            'guest' => $reservation->guest->fresh()
        ]);
    }

    /**
     * Get booking details for auto-fetch
     */
    public function getBookingDetails($reservationUuid)
    {
        $reservation = Reservation::with(['guest', 'accommodation.property'])
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
                'special_requests' => $reservation->special_requests,
            ],
            'guest' => [
                'name' => $reservation->guest->name,
                'email' => $reservation->guest->email,
                'mobile_number' => $reservation->guest->mobile_number,
                'address' => $reservation->guest->address,
                'id_type' => $reservation->guest->id_type,
                'id_number' => $reservation->guest->id_number,
            ],
            'accommodation' => [
                'display_name' => $reservation->accommodation->display_name,
                'room_type' => $reservation->accommodation->room_type,
                'property_name' => $reservation->accommodation->property->name,
            ]
        ]);
    }
}
