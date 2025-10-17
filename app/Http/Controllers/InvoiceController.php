<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function download(Reservation $booking)
    {
        // Ensure the booking belongs to the authenticated user's property
        $this->authorize('view', $booking);
        
        return $this->generateInvoicePdf($booking);
    }

    public function publicDownload(Reservation $booking)
    {
        // No authorization check for public access
        return $this->generateInvoicePdf($booking);
    }

    private function generateInvoicePdf(Reservation $booking)
    {
        // Load all necessary relationships
        $booking->load([
            'guest',
            'accommodation.property.location.city.district.state',
            'accommodation.property.owner',
            'b2bPartner',
            'commission'
        ]);

        // Calculate additional details
        $nights = $booking->check_in_date->diffInDays($booking->check_out_date);
        $invoiceData = [
            'booking' => $booking,
            'nights' => $nights,
            'invoice_number' => 'INV-' . strtoupper(substr($booking->uuid, 0, 8)) . '-' . $booking->id,
            'invoice_date' => now()->format('d/m/Y'),
            'due_date' => $booking->check_in_date->format('d/m/Y'),
            'property_owner' => $booking->accommodation->property->owner,
            'property_location' => $booking->accommodation->property->location,
            'accommodation_details' => $booking->accommodation,
            'guest_details' => $booking->guest,
            'b2b_partner' => $booking->b2bPartner,
            'commission_details' => $booking->commission,
            'is_b2b_booking' => $booking->isB2bBooking(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('invoices.booking-invoice', $invoiceData);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Invoice-' . $booking->confirmation_number . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }


    public function bulkDownload(Request $request)
    {
        $statuses = $request->input('statuses', []);
        
        if (empty($statuses)) {
            return redirect()->back()->with('error', 'Please select at least one booking status.');
        }

        // Get bookings based on selected statuses
        $query = Reservation::with([
            'guest',
            'accommodation.property.location.city.district.state',
            'accommodation.property.owner',
            'b2bPartner',
            'commission'
        ])->whereHas('accommodation.property', function($q) {
            $q->where('owner_id', auth()->id());
        });

        if (!in_array('all', $statuses)) {
            $query->whereIn('status', $statuses);
        }

        $bookings = $query->latest()->get();

        if ($bookings->isEmpty()) {
            return redirect()->back()->with('error', 'No bookings found for the selected statuses.');
        }

        // Prepare bulk invoice data
        $bulkInvoiceData = [
            'bookings' => $bookings,
            'total_bookings' => $bookings->count(),
            'total_amount' => $bookings->sum('total_amount'),
            'total_advance_paid' => $bookings->sum('advance_paid'),
            'total_balance_pending' => $bookings->sum('balance_pending'),
            'status_counts' => [
                'pending' => $bookings->where('status', 'pending')->count(),
                'confirmed' => $bookings->where('status', 'confirmed')->count(),
                'cancelled' => $bookings->where('status', 'cancelled')->count(),
            ],
            'property_owner' => auth()->user(),
            'generated_date' => now()->format('d/m/Y H:i:s'),
            'invoice_number' => 'BULK-' . strtoupper(substr(Str::uuid(), 0, 8)) . '-' . now()->format('Ymd'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('invoices.bulk-invoice', $bulkInvoiceData);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Bulk-Invoices-' . implode('-', $statuses) . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
