<?php

namespace App\Observers;

use App\Models\BookingFinance;
use App\Models\IncomeRecord;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     * Create a BookingFinance record immediately when a booking is made.
     */
    public function created(Reservation $reservation): void
    {
        $this->createBookingFinance($reservation);
    }

    /**
     * Handle the Reservation "updated" event.
     * Sync BookingFinance and create IncomeRecord on checkout.
     */
    public function updated(Reservation $reservation): void
    {
        // Sync BookingFinance on any update
        $this->syncBookingFinance($reservation);

        // Create IncomeRecord on checkout (existing behavior)
        if ($reservation->isDirty('status') && $reservation->status === 'checked_out') {
            $this->syncToIncomeRecord($reservation);
        }
    }

    /**
     * Create a BookingFinance record for a new reservation.
     */
    protected function createBookingFinance(Reservation $reservation): void
    {
        // Skip if already exists
        if (BookingFinance::where('reservation_id', $reservation->id)->exists()) {
            return;
        }

        try {
            BookingFinance::createFromReservation($reservation);

            Log::info('BookingFinance created from reservation', [
                'reservation_id' => $reservation->id,
                'confirmation_number' => $reservation->confirmation_number,
                'total_amount' => $reservation->total_amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create BookingFinance from reservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sync BookingFinance record when reservation is updated.
     */
    protected function syncBookingFinance(Reservation $reservation): void
    {
        $bookingFinance = $reservation->bookingFinance;

        if (!$bookingFinance) {
            // Create if doesn't exist (for legacy reservations)
            $this->createBookingFinance($reservation);
            return;
        }

        try {
            $bookingFinance->updateFromReservation();

            Log::info('BookingFinance synced from reservation update', [
                'reservation_id' => $reservation->id,
                'booking_finance_id' => $bookingFinance->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync BookingFinance', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sync a checked-out reservation to income record.
     */
    protected function syncToIncomeRecord(Reservation $reservation): void
    {
        // Skip if already synced
        if (IncomeRecord::where('reservation_id', $reservation->id)->exists()) {
            return;
        }

        // Skip if no amount
        if ($reservation->total_amount <= 0) {
            return;
        }

        try {
            $accommodation = $reservation->accommodation;
            if (!$accommodation) {
                return;
            }

            $property = $accommodation->property;
            if (!$property) {
                return;
            }

            $incomeType = $reservation->b2b_partner_id ? 'b2b_booking' : 'booking';
            $paidAmount = $reservation->total_amount - ($reservation->balance_pending ?? 0);
            $paymentStatus = 'paid';

            if ($reservation->balance_pending > 0 && $reservation->balance_pending < $reservation->total_amount) {
                $paymentStatus = 'partial';
            } elseif ($reservation->balance_pending >= $reservation->total_amount) {
                $paymentStatus = 'unpaid';
            }

            IncomeRecord::create([
                'property_id' => $property->id,
                'accommodation_id' => $accommodation->id,
                'b2b_partner_id' => $reservation->b2b_partner_id,
                'reservation_id' => $reservation->id,
                'income_type' => $incomeType,
                'amount' => $reservation->total_amount,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'transaction_date' => $reservation->checked_out_at ?? now(),
                'reference_number' => $reservation->confirmation_number,
                'notes' => "Auto-generated from checkout - {$reservation->confirmation_number}",
            ]);

            Log::info('IncomeRecord created from checkout', [
                'reservation_id' => $reservation->id,
                'confirmation_number' => $reservation->confirmation_number,
                'amount' => $reservation->total_amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create IncomeRecord from checkout', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

