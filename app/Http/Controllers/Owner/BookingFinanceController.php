<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookingFinance;
use App\Models\Property;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingFinanceController extends Controller
{
    /**
     * Display the all-in-one finance dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        // Build query with filters
        $query = BookingFinance::whereIn('property_id', $propertyIds)
            ->with(['reservation.guest', 'property', 'accommodation', 'b2bPartner']);

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by booking status
        if ($request->filled('booking_status')) {
            $query->where('booking_status', $request->booking_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }

        // Get finances with pagination
        $finances = $query->latest('booking_date')->paginate(20)->withQueryString();

        // Calculate summary stats
        $summaryQuery = BookingFinance::whereIn('property_id', $propertyIds);

        $summary = [
            'total_bookings' => $summaryQuery->count(),
            'total_amount' => $summaryQuery->sum('total_amount'),
            'total_received' => $summaryQuery->sum('advance_received'),
            'total_pending' => $summaryQuery->sum('balance_pending'),
            'today_collections' => BookingFinance::whereIn('property_id', $propertyIds)
                ->whereDate('last_payment_date', today())
                ->sum('advance_received'),
            'pending_count' => $summaryQuery->clone()->pending()->count(),
            'paid_count' => $summaryQuery->clone()->paid()->count(),
        ];

        // Get properties for filter dropdown
        $properties = Property::where('owner_id', $user->id)->get(['id', 'name']);

        return view('owner.booking-finance.index', compact('finances', 'summary', 'properties'));
    }

    /**
     * Display a single finance record.
     */
    public function show(BookingFinance $bookingFinance)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        // Authorization check
        if (!$propertyIds->contains($bookingFinance->property_id)) {
            abort(403, 'Unauthorized');
        }

        $bookingFinance->load([
            'reservation.guest',
            'reservation.checkInRecord',
            'reservation.checkOutRecord',
            'property',
            'accommodation',
            'b2bPartner',
        ]);

        return view('owner.booking-finance.show', compact('bookingFinance'));
    }

    /**
     * Record a payment for a booking.
     */
    public function recordPayment(Request $request, BookingFinance $bookingFinance)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        if (!$propertyIds->contains($bookingFinance->property_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $bookingFinance->recordPayment($validated['amount'], $validated['notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => [
                    'advance_received' => $bookingFinance->advance_received,
                    'balance_pending' => $bookingFinance->balance_pending,
                    'payment_status' => $bookingFinance->payment_status,
                    'payment_status_label' => $bookingFinance->payment_status_label,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record additional charges for a booking.
     */
    public function recordCharge(Request $request, BookingFinance $bookingFinance)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        if (!$propertyIds->contains($bookingFinance->property_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $bookingFinance->recordAdditionalCharge($validated['amount'], $validated['reason'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Additional charge recorded successfully',
                'data' => [
                    'additional_charges' => $bookingFinance->additional_charges,
                    'final_amount' => $bookingFinance->final_amount,
                    'balance_pending' => $bookingFinance->balance_pending,
                    'payment_status' => $bookingFinance->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record charge: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record a refund for a booking.
     */
    public function recordRefund(Request $request, BookingFinance $bookingFinance)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        if (!$propertyIds->contains($bookingFinance->property_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $bookingFinance->advance_received,
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $bookingFinance->recordRefund($validated['amount'], $validated['reason'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Refund recorded successfully',
                'data' => [
                    'refund_amount' => $bookingFinance->refund_amount,
                    'final_amount' => $bookingFinance->final_amount,
                    'balance_pending' => $bookingFinance->balance_pending,
                    'payment_status' => $bookingFinance->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record refund: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get summary data for AJAX requests.
     */
    public function summary(Request $request)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        $query = BookingFinance::whereIn('property_id', $propertyIds);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Date range for period comparison
        $startDate = $request->filled('date_from')
            ? Carbon::parse($request->date_from)
            : now()->startOfMonth();
        $endDate = $request->filled('date_to')
            ? Carbon::parse($request->date_to)
            : now()->endOfMonth();

        $periodQuery = $query->clone()->whereBetween('booking_date', [$startDate, $endDate]);

        return response()->json([
            'total_amount' => $periodQuery->sum('total_amount'),
            'total_received' => $periodQuery->sum('advance_received'),
            'total_pending' => $periodQuery->sum('balance_pending'),
            'booking_count' => $periodQuery->count(),
            'paid_count' => $periodQuery->clone()->paid()->count(),
            'pending_count' => $periodQuery->clone()->pending()->count(),
        ]);
    }

    /**
     * Export finances to CSV.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        $query = BookingFinance::whereIn('property_id', $propertyIds)
            ->with(['reservation.guest', 'property', 'accommodation']);

        // Apply same filters as index
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }

        $finances = $query->latest('booking_date')->get();

        $filename = 'booking-finances-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($finances) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Finance #',
                'Booking #',
                'Guest Name',
                'Property',
                'Accommodation',
                'Booking Date',
                'Check-in',
                'Check-out',
                'Total Amount',
                'Received',
                'Pending',
                'Payment Status',
                'Booking Status',
            ]);

            foreach ($finances as $finance) {
                fputcsv($file, [
                    $finance->finance_number,
                    $finance->reservation?->confirmation_number ?? 'N/A',
                    $finance->reservation?->guest?->name ?? 'N/A',
                    $finance->property?->name ?? 'N/A',
                    $finance->accommodation?->custom_name ?? 'N/A',
                    $finance->booking_date->format('Y-m-d'),
                    $finance->check_in_date->format('Y-m-d'),
                    $finance->check_out_date->format('Y-m-d'),
                    $finance->total_amount,
                    $finance->advance_received,
                    $finance->balance_pending,
                    $finance->payment_status_label,
                    $finance->booking_status_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
