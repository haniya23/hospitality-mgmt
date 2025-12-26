<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Get owner bookings list
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->input('status'); // all, confirmed, pending, cancelled

        $query = $user->reservations()
            ->with(['guest', 'accommodation.property'])
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->has('check_in_date')) {
            $query->whereDate('check_in_date', $request->input('check_in_date'));
        }
        if ($request->has('check_in_date_from')) {
            $query->whereDate('check_in_date', '>=', $request->input('check_in_date_from'));
        }

        if ($request->has('check_out_date')) {
            $query->whereDate('check_out_date', $request->input('check_out_date'));
        }
        if ($request->has('check_out_date_from')) {
            $query->whereDate('check_out_date', '>=', $request->input('check_out_date_from'));
        }

        $bookings = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Get booking counts by status
     */
    public function counts(Request $request) 
    {
        $user = $request->user();
        
        $counts = [
            'all' => $user->reservations()->count(),
            'pending' => $user->reservations()->where('status', 'pending')->count(),
            'confirmed' => $user->reservations()->where('status', 'confirmed')->count(),
            'cancelled' => $user->reservations()->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $counts
        ]);
    }

    /**
     * Store new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'accommodation_id' => 'required|exists:property_accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'booking_type' => 'required|in:per_day,per_person',
            'guest_name' => 'nullable|string',
            'guest_mobile' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'b2b_partner_id' => 'nullable|exists:b2b_partners,uuid',
            'guest_id' => 'nullable|exists:guests,id', // Can pass direct ID
        ]);

        // Handle Guest
        if ($request->guest_id) {
            $guest = \App\Models\Guest::find($request->guest_id);
        } else {
             $guest = \App\Models\Guest::firstOrCreate(
                ['mobile_number' => $request->guest_mobile],
                [
                    'name' => $request->guest_name,
                    'email' => $request->guest_email
                ]
            );
        }

        // Handle Partner
        $partner = null;
        if ($validated['b2b_partner_id']) {
            $partner = \App\Models\B2bPartner::where('uuid', $validated['b2b_partner_id'])->first();
        }

        $booking = \App\Models\Reservation::create([
            'guest_id' => $guest->id,
            'property_accommodation_id' => $validated['accommodation_id'],
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'adults' => $validated['adults'],
            'children' => $validated['children'],
            'booking_type' => $validated['booking_type'] ?? 'per_day', // Fallback
            'total_amount' => $validated['total_amount'],
            'advance_paid' => $validated['advance_paid'],
            'balance_pending' => $validated['total_amount'] - $validated['advance_paid'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
            'created_by' => auth()->id(),
            'b2b_partner_id' => $partner ? $partner->id : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ]);
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,pending,checked_in,checked_out',
            'reason' => 'nullable|string|required_if:status,cancelled',
        ]);

        $booking = $request->user()->reservations()->findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        // Handle Cancellation Reason
        if ($validated['status'] === 'cancelled' && $request->reason) {
             \App\Models\CancelledBooking::create([
                'reservation_id' => $booking->id,
                'reason' => $request->reason,
                'cancelled_by' => $request->user()->id,
                'cancelled_at' => now(),
                'refund_amount' => 0 // Default or calculate logic
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => $booking
        ]);
    }

    /**
     * Update booking details
     */
    public function update(Request $request, $id)
    {
        $booking = $request->user()->reservations()->findOrFail($id);
        
        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'guest_name' => 'nullable|string',
            'guest_mobile' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
        ]);
        
        // Update Guest if exists (optional logic, maybe just update booking snapshot)
        if ($booking->guest) {
             $booking->guest->update([
                 'name' => $validated['guest_name'] ?? $booking->guest->name,
                 'mobile_number' => $validated['guest_mobile'] ?? $booking->guest->mobile_number,
                 'email' => $validated['guest_email'] ?? $booking->guest->email,
             ]);
        }
        
        $booking->update([
             'check_in_date' => $validated['check_in_date'],
             'check_out_date' => $validated['check_out_date'],
             'adults' => $validated['adults'],
             'children' => $validated['children'],
             'total_amount' => $validated['total_amount'],
             'advance_paid' => $validated['advance_paid'],
             'balance_pending' => $validated['total_amount'] - $validated['advance_paid'],
             'special_requests' => $validated['special_requests'],
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking
        ]);
    }

    /**
     * Download Invoice
     */
    public function downloadInvoice(Request $request, $id)
    {
        $booking = $request->user()->reservations()->findOrFail($id);
        return app(\App\Http\Controllers\InvoiceController::class)->download($booking);
    }
}
