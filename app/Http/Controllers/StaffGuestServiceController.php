<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\CheckIn;
use App\Models\CheckOut;
use Carbon\Carbon;

class StaffGuestServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('staff');
    }

    /**
     * Show guest service dashboard
     */
    public function index()
    {
        $staff = Auth::user();
        
        // Check if staff has guest service permissions
        if (!$staff->hasGuestServiceAccess()) {
            abort(403, 'You do not have permission to access guest services.');
        }

        $staffAssignments = $staff->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();

        // Get today's bookings
        $todayBookings = Reservation::whereHas('propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->whereDate('check_in_date', Carbon::today())
            ->where('status', 'confirmed')
            ->with(['guest', 'property', 'accommodation'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        // Get today's check-outs
        $todayCheckouts = Reservation::whereHas('propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->whereDate('check_out_date', Carbon::today())
            ->where('status', 'confirmed')
            ->with(['guest', 'property', 'accommodation'])
            ->orderBy('check_out_date', 'asc')
            ->get();

        // Get pending check-ins (not yet checked in)
        $pendingCheckins = Reservation::whereHas('propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->whereDate('check_in_date', Carbon::today())
            ->where('status', 'confirmed')
            ->whereDoesntHave('checkInRecord')
            ->with(['guest', 'property', 'accommodation'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        // Get pending check-outs (not yet checked out)
        $pendingCheckouts = Reservation::whereHas('propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->whereDate('check_out_date', Carbon::today())
            ->where('status', 'confirmed')
            ->whereDoesntHave('checkOutRecord')
            ->with(['guest', 'property', 'accommodation'])
            ->orderBy('check_out_date', 'asc')
            ->get();

        // Get completed check-ins today
        $completedCheckins = CheckIn::whereHas('reservation.propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->whereDate('created_at', Carbon::today())
            ->with(['reservation.guest', 'reservation.property', 'reservation.accommodation'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get completed check-outs today
        $completedCheckouts = CheckOut::whereHas('reservation.propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->whereDate('created_at', Carbon::today())
            ->with(['reservation.guest', 'reservation.property', 'reservation.accommodation'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.guest-service.index', compact(
            'todayBookings',
            'todayCheckouts',
            'pendingCheckins',
            'pendingCheckouts',
            'completedCheckins',
            'completedCheckouts'
        ));
    }

    /**
     * Show bookings calendar
     */
    public function calendar()
    {
        $staff = Auth::user();
        
        // All staff can view bookings (upcoming bookings)

        $staffAssignments = $staff->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();

        // Get bookings for the current month
        $bookings = Reservation::whereHas('propertyAccommodation', function($query) use ($propertyIds) {
            $query->whereIn('property_id', $propertyIds);
        })
            ->where('status', 'confirmed')
            ->whereBetween('check_in_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()->addDays(7) // Show a bit into next month
            ])
            ->with(['guest', 'property', 'accommodation'])
            ->get();

        return view('staff.guest-service.calendar', compact('bookings'));
    }

    /**
     * Process check-in
     */
    public function checkIn(Request $request, $reservationId)
    {
        $staff = Auth::user();
        
        // Check permissions
        if (!$staff->hasGuestServiceAccess()) {
            abort(403, 'You do not have permission to process check-ins.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'id_verified' => 'required|boolean',
            'payment_verified' => 'required|boolean',
        ]);

        $reservation = Reservation::findOrFail($reservationId);
        
        // Verify staff has access to this property
        $staffAssignments = $staff->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();
        
        if (!in_array($reservation->property->id, $propertyIds)) {
            abort(403, 'You do not have access to this property.');
        }

        // Check if already checked in
        if ($reservation->checkInRecord()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Guest has already been checked in.'
            ]);
        }

        // Create check-in record
        $checkIn = CheckIn::create([
            'reservation_id' => $reservation->id,
            'property_id' => $reservation->property->id,
            'guest_id' => $reservation->guest_id,
            'checked_in_by' => $staff->id,
            'check_in_time' => Carbon::now(),
            'id_verified' => $request->id_verified,
            'payment_verified' => $request->payment_verified,
            'notes' => $request->notes,
            'status' => 'completed'
        ]);

        // Update reservation status
        $reservation->update(['status' => 'checked_in']);

        return response()->json([
            'success' => true,
            'message' => 'Check-in completed successfully.',
            'checkIn' => $checkIn->load('reservation.guest')
        ]);
    }

    /**
     * Process check-out
     */
    public function checkOut(Request $request, $reservationId)
    {
        $staff = Auth::user();
        
        // Check permissions
        if (!$staff->hasGuestServiceAccess()) {
            abort(403, 'You do not have permission to process check-outs.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'room_condition' => 'required|in:excellent,good,fair,poor',
            'amenities_returned' => 'required|boolean',
        ]);

        $reservation = Reservation::findOrFail($reservationId);
        
        // Verify staff has access to this property
        $staffAssignments = $staff->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();
        
        if (!in_array($reservation->property->id, $propertyIds)) {
            abort(403, 'You do not have access to this property.');
        }

        // Check if already checked out
        if ($reservation->checkOutRecord()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Guest has already been checked out.'
            ]);
        }

        // Create check-out record
        $checkOut = CheckOut::create([
            'reservation_id' => $reservation->id,
            'property_id' => $reservation->property->id,
            'guest_id' => $reservation->guest_id,
            'checked_out_by' => $staff->id,
            'check_out_time' => Carbon::now(),
            'room_condition' => $request->room_condition,
            'amenities_returned' => $request->amenities_returned,
            'notes' => $request->notes,
            'status' => 'completed'
        ]);

        // Update reservation status
        $reservation->update(['status' => 'checked_out']);

        return response()->json([
            'success' => true,
            'message' => 'Check-out completed successfully.',
            'checkOut' => $checkOut->load('reservation.guest')
        ]);
    }

    /**
     * Get booking details for modal
     */
    public function getBookingDetails($reservationId)
    {
        $staff = Auth::user();
        
        $reservation = Reservation::with(['guest', 'property', 'accommodation', 'checkIns', 'checkOuts'])
            ->findOrFail($reservationId);
        
        // Verify staff has access to this property
        $staffAssignments = $staff->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();
        
        if (!in_array($reservation->property->id, $propertyIds)) {
            abort(403, 'You do not have access to this property.');
        }

        return response()->json([
            'success' => true,
            'reservation' => $reservation
        ]);
    }
}
