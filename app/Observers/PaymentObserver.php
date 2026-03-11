<?php

namespace App\Observers;

use App\Models\IncomeRecord;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event - for successful payments.
     */
    public function created(Payment $payment): void
    {
        $this->syncPaymentToIncomeRecord($payment);
    }

    /**
     * Handle the Payment "updated" event - for status changes to completed.
     */
    public function updated(Payment $payment): void
    {
        // If payment was just marked as completed, create income record
        if ($payment->isDirty('status') && $payment->status === 'completed') {
            $this->syncPaymentToIncomeRecord($payment);
        }
    }

    /**
     * Sync a payment to income record if it's completed and from an invoice.
     */
    protected function syncPaymentToIncomeRecord(Payment $payment): void
    {
        // Only process completed payments with invoices
        if ($payment->status !== 'completed' || !$payment->invoice_id) {
            return;
        }

        // Check if income record already exists for this payment
        $existingRecord = IncomeRecord::where('payment_id', $payment->id)->first();
        if ($existingRecord) {
            return;
        }

        try {
            $invoice = $payment->invoice;
            if (!$invoice) {
                return;
            }

            $reservation = $invoice->reservation;
            if (!$reservation) {
                return;
            }

            $accommodation = $reservation->accommodation;
            if (!$accommodation) {
                return;
            }

            $property = $accommodation->property;
            if (!$property) {
                return;
            }

            // Determine income type based on reservation source
            $incomeType = 'booking';
            if ($reservation->b2b_partner_id) {
                $incomeType = 'b2b_booking';
            }

            // Create income record
            IncomeRecord::create([
                'property_id' => $property->id,
                'accommodation_id' => $accommodation->id,
                'b2b_partner_id' => $reservation->b2b_partner_id,
                'reservation_id' => $reservation->id,
                'payment_id' => $payment->id,
                'income_type' => $incomeType,
                'amount' => $payment->amount,
                'paid_amount' => $payment->amount,
                'payment_status' => 'paid',
                'transaction_date' => $payment->paid_at ?? $payment->created_at,
                'reference_number' => $payment->reference_number ?? $reservation->booking_reference ?? null,
                'notes' => "Auto-generated from booking payment - {$reservation->booking_reference}",
            ]);

            Log::info('IncomeRecord created from payment', [
                'payment_id' => $payment->id,
                'reservation_id' => $reservation->id,
                'amount' => $payment->amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create IncomeRecord from payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
