<?php

namespace App\Services;

use App\Models\PropertyAccommodation;
use App\Models\PricingRule;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\Reservation;
use App\Models\Commission;
use App\Models\AuditLog;
use Carbon\Carbon;

class BookingService
{
    public function calculateRate($accommodationId, $checkIn, $checkOut, $b2bPartnerId = null)
    {
        $accommodation = PropertyAccommodation::find($accommodationId);
        if (!$accommodation) return null;

        $baseRate = (float)($accommodation->base_rate ?? 0);
        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        
        $adjustedRate = $baseRate;
        $discounts = [];
        
        try {
            $pricingRules = PricingRule::getApplicableRules(
                $accommodation->property_id,
                $checkIn,
                $checkOut,
                $b2bPartnerId
            );

            foreach ($pricingRules as $rule) {
                $newRate = $rule->calculateAdjustedRate($adjustedRate);
                $discounts[] = [
                    'name' => $rule->rule_name,
                    'adjustment' => $newRate - $adjustedRate,
                ];
                $adjustedRate = $newRate;
            }
        } catch (\Exception $e) {
            // Fallback if pricing rules fail
        }

        return [
            'base_rate' => $baseRate,
            'adjusted_rate' => $adjustedRate,
            'nights' => $nights,
            'total_amount' => $adjustedRate * $nights,
            'discounts' => $discounts
        ];
    }

    public function createBooking($data)
    {
        // Create guest if needed
        if ($data['create_new_guest']) {
            $guest = Guest::create([
                'name' => $data['guest_name'],
                'mobile_number' => $data['guest_mobile'],
                'email' => $data['guest_email'] ?? null,
            ]);
            $data['guest_id'] = $guest->id;
        }

        // Create B2B partner if needed
        if ($data['create_new_partner'] && $data['partner_mobile']) {
            $partner = B2bPartner::createPartnershipRequest(
                auth()->id(),
                $data['partner_mobile'],
                $data['partner_name']
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
            'total_amount' => $data['rate_override'] ?? $data['total_amount'],
            'advance_paid' => $data['advance_paid'],
            'balance_pending' => ($data['rate_override'] ?? $data['total_amount']) - $data['advance_paid'],
            'rate_override' => $data['rate_override'],
            'override_reason' => $data['override_reason'],
            'special_requests' => $data['special_requests'],
            'notes' => $data['notes'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Log price override if applicable
        try {
            if ($data['rate_override'] && $data['rate_override'] != $data['total_amount']) {
                AuditLog::logPriceOverride($booking, $data['total_amount'], $data['rate_override'], $data['override_reason']);
            }
        } catch (\Exception $e) {
            // Fallback if audit log fails
        }

        // Create commission record for B2B bookings
        try {
            if ($booking->b2b_partner_id) {
                Commission::calculateForBooking($booking);
            }
        } catch (\Exception $e) {
            // Fallback if commission calculation fails
        }

        return $booking;
    }
}