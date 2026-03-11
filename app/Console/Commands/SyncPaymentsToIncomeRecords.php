<?php

namespace App\Console\Commands;

use App\Models\IncomeRecord;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Console\Command;

class SyncPaymentsToIncomeRecords extends Command
{
    protected $signature = 'finance:sync-payments {--force : Force sync even if records exist}';
    protected $description = 'Sync existing completed payments and checkouts to income records';

    public function handle(): int
    {
        $this->info('Syncing completed bookings to income records...');

        $created = 0;
        $skipped = 0;

        // First, sync from Payments table if any
        $payments = Payment::with(['invoice.reservation.accommodation.property'])
            ->where('status', 'completed')
            ->whereNotNull('invoice_id')
            ->get();

        foreach ($payments as $payment) {
            $exists = IncomeRecord::where('payment_id', $payment->id)->exists();

            if ($exists && !$this->option('force')) {
                $skipped++;
                continue;
            }

            $invoice = $payment->invoice;
            $reservation = $invoice?->reservation;
            $accommodation = $reservation?->accommodation;
            $property = $accommodation?->property;

            if (!$property) {
                $skipped++;
                continue;
            }

            $incomeType = $reservation->b2b_partner_id ? 'b2b_booking' : 'booking';

            IncomeRecord::updateOrCreate(
                ['payment_id' => $payment->id],
                [
                    'property_id' => $property->id,
                    'accommodation_id' => $accommodation->id,
                    'b2b_partner_id' => $reservation->b2b_partner_id,
                    'reservation_id' => $reservation->id,
                    'income_type' => $incomeType,
                    'amount' => $payment->amount,
                    'paid_amount' => $payment->amount,
                    'payment_status' => 'paid',
                    'transaction_date' => $payment->paid_at ?? $payment->created_at,
                    'reference_number' => $payment->reference_number ?? $reservation->confirmation_number,
                    'notes' => "Synced from payment - {$reservation->confirmation_number}",
                ]
            );
            $created++;
        }

        // Also sync from checked-out reservations that don't have income records yet
        $this->info('Syncing checked-out reservations...');

        $reservations = Reservation::with(['accommodation.property'])
            ->where('status', 'checked_out')
            ->where('total_amount', '>', 0)
            ->get();

        foreach ($reservations as $reservation) {
            // Check if income record exists for this reservation
            $exists = IncomeRecord::where('reservation_id', $reservation->id)->exists();

            if ($exists && !$this->option('force')) {
                $skipped++;
                continue;
            }

            $accommodation = $reservation->accommodation;
            $property = $accommodation?->property;

            if (!$property) {
                $this->warn("Reservation #{$reservation->id}: No property found");
                $skipped++;
                continue;
            }

            $incomeType = $reservation->b2b_partner_id ? 'b2b_booking' : 'booking';
            $paidAmount = $reservation->total_amount - ($reservation->balance_pending ?? 0);
            $paymentStatus = $reservation->balance_pending > 0 ? 'partial' : 'paid';

            if ($reservation->balance_pending >= $reservation->total_amount) {
                $paymentStatus = 'unpaid';
            }

            IncomeRecord::updateOrCreate(
                ['reservation_id' => $reservation->id],
                [
                    'property_id' => $property->id,
                    'accommodation_id' => $accommodation->id,
                    'b2b_partner_id' => $reservation->b2b_partner_id,
                    'income_type' => $incomeType,
                    'amount' => $reservation->total_amount,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $paymentStatus,
                    'transaction_date' => $reservation->checked_out_at ?? $reservation->check_out_date,
                    'reference_number' => $reservation->confirmation_number,
                    'notes' => "Synced from checkout - {$reservation->confirmation_number}",
                ]
            );

            $created++;
            $this->line("Synced reservation #{$reservation->confirmation_number} (₹{$reservation->total_amount})");
        }

        $this->info("Done! Created: {$created}, Skipped: {$skipped}");
        return Command::SUCCESS;
    }
}
