<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\Reservation;
use App\Models\Commission;
use App\Models\AuditLog;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Create guest if needed
            if ($request->create_new_guest) {
                $guest = Guest::create([
                    'name' => $data['guest_name'],
                    'mobile_number' => $data['guest_mobile'],
                    'email' => $request->guest_email,
                ]);
                $data['guest_id'] = $guest->id;
            }

            // Create B2B partner if needed
            if ($request->create_new_partner && $request->partner_mobile) {
                $partner = B2bPartner::createPartnershipRequest(
                    auth()->id(),
                    $request->partner_mobile,
                    $request->partner_name
                );
                $data['b2b_partner_id'] = $partner->id;
            }

            // Create booking
            $booking = Reservation::create([
                'guest_id' => $data['guest_id'],
                'property_accommodation_id' => $data['accommodation_id'],
                'b2b_partner_id' => $data['b2b_partner_id'] ?? null,
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'adults' => $data['adults'],
                'children' => $data['children'],
                'total_amount' => $request->rate_override ?? $data['total_amount'],
                'advance_paid' => $data['advance_paid'],
                'balance_pending' => ($request->rate_override ?? $data['total_amount']) - $data['advance_paid'],
                'rate_override' => $request->rate_override,
                'override_reason' => $request->override_reason,
                'special_requests' => $request->special_requests,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Log price override if applicable
            if ($request->rate_override && $request->rate_override != $data['total_amount']) {
                AuditLog::logPriceOverride($booking, $data['total_amount'], $request->rate_override, $request->override_reason);
            }

            // Create commission record for B2B bookings
            if ($booking->b2b_partner_id) {
                Commission::calculateForBooking($booking);
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking_id' => $booking->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage()
            ], 500);
        }
    }
}