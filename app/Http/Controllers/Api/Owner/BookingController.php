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

        $query = $this->getOwnerReservationsQuery($user)
            ->with(['guest', 'accommodation.property', 'accommodation.photos', 'cancelledBooking'])
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

        // Append refund details at the booking root level for mobile convenience.
        $bookings->getCollection()->transform(function ($booking) {
            $booking->refund_amount = $booking->cancelledBooking?->refund_amount ?? 0;
            $booking->remaining_refundable_amount = max(0, (float) $booking->advance_paid - (float) $booking->refund_amount);
            return $booking;
        });

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
        $ownerQuery = $this->getOwnerReservationsQuery($user);
        
        $counts = [
            'all' => (clone $ownerQuery)->count(),
            'pending' => (clone $ownerQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $ownerQuery)->where('status', 'confirmed')->count(),
            'cancelled' => (clone $ownerQuery)->where('status', 'cancelled')->count(),
            'completed' => (clone $ownerQuery)->where('status', 'checked_out')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $counts
        ]);
    }

    /**
     * Get booking details
     */
    public function show(Request $request, $id)
    {
        $booking = $this->getOwnerReservationsQuery($request->user())
            ->with(['guest', 'accommodation.property'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $booking
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

        $booking = $this->getOwnerReservationsQuery($request->user())->findOrFail($id);
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
        $booking = $this->getOwnerReservationsQuery($request->user())->findOrFail($id);
        
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
        $booking = $this->getOwnerReservationsQuery($request->user())->findOrFail($id);
        return app(\App\Http\Controllers\InvoiceController::class)->download($booking);
    }

    /**
     * Update Booking Payment
     */
    public function updatePayment(Request $request, $id)
    {
        try {
            $booking = $this->getOwnerReservationsQuery($request->user())
                ->with(['accommodation.property'])
                ->where(function ($query) use ($id) {
                    if (is_numeric($id)) {
                        $query->where('id', $id);
                    } else {
                        $query->where('uuid', $id);
                    }
                })
                ->firstOrFail();
            
            $request->validate([
                'amount_paid' => 'required|numeric|min:0',
                'payment_notes' => 'nullable|string|max:500'
            ]);
            
            $amountPaid = $request->amount_paid;
            $newBalance = max(0, $booking->balance_pending - $amountPaid);
            
            $booking->update([
                'balance_pending' => $newBalance,
                'advance_paid' => $booking->advance_paid + $amountPaid
            ]);

            // Sync/Create Income Record
            $incomeType = $booking->b2b_partner_id ? 'b2b_booking' : 'booking';
            $paidAmount = $booking->total_amount - $newBalance;
            $paymentStatus = $newBalance > 0 ? 'partial' : 'paid';

            if ($newBalance >= $booking->total_amount) {
                $paymentStatus = 'unpaid';
            }

            \App\Models\IncomeRecord::updateOrCreate(
                ['reservation_id' => $booking->id],
                [
                    'property_id' => $booking->accommodation->property->id,
                    'accommodation_id' => $booking->accommodation->id,
                    'b2b_partner_id' => $booking->b2b_partner_id,
                    'income_type' => $incomeType,
                    'amount' => $booking->total_amount,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $paymentStatus,
                    'transaction_date' => now(),
                    'reference_number' => $booking->confirmation_number,
                    'notes' => $request->payment_notes ?? "Payment update - {$booking->confirmation_number}",
                ]
            );
            
            // Update checkout record payment status if fully paid
            if ($newBalance == 0 && $booking->checkOutRecord) {
                $booking->checkOutRecord->update([
                    'payment_status' => 'completed',
                    'payment_notes' => $request->payment_notes
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'new_balance' => $newBalance
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function recordRefund(Request $request, $id)
    {
        try {
            $booking = $this->getOwnerReservationsQuery($request->user())
                ->with(['accommodation.property', 'bookingFinance', 'cancelledBooking'])
                ->where(function($query) use ($id) {
                    if (is_numeric($id)) {
                        $query->where('id', $id)->orWhere('uuid', $id);
                    } else {
                        $query->where('uuid', $id);
                    }
                })
                ->firstOrFail();
            
            if ($booking->status !== 'cancelled') {
                return response()->json(['success' => false, 'message' => 'Booking is not cancelled'], 400);
            }

            $existingRefund = (float) ($booking->bookingFinance?->refund_amount
                ?? $booking->cancelledBooking?->refund_amount
                ?? 0);
            $remainingRefundable = max(0, (float) $booking->advance_paid - $existingRefund);

            if ($remainingRefundable <= 0) {
                return response()->json(['success' => false, 'message' => 'No refundable collected amount remaining'], 422);
            }
            
            $request->validate([
                'amount' => 'required|numeric|min:0.01|max:' . $remainingRefundable,
                'reason' => 'nullable|string|max:255'
            ]);
            
            $amount = $request->amount;
            $reason = $request->reason;
            
            $bookingFinance = $booking->bookingFinance;
            if (!$bookingFinance) {
                $bookingFinance = \App\Models\BookingFinance::createFromReservation($booking);
            }
            
            $bookingFinance->recordRefund($amount, $reason);
            
            $cancelledBooking = $booking->cancelledBooking;
            if ($cancelledBooking) {
                $cancelledBooking->update([
                    'refund_amount' => $bookingFinance->refund_amount
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Refund recorded successfully',
                'refund_amount' => $bookingFinance->refund_amount,
                'remaining_refundable_amount' => max(0, $bookingFinance->advance_received - $bookingFinance->refund_amount),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getOwnerReservationsQuery($user)
    {
        return \App\Models\Reservation::whereHas('accommodation.property', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        });
    }
}
