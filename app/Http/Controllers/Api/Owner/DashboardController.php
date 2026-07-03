<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\B2bPartner;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics and data
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Properties
        $properties = $user->properties()->with(['category', 'location.city.district.state'])->latest()->get();
        
        // Next Bookings
        $nextBookings = Reservation::whereHas('accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'confirmed')
        ->where('check_in_date', '>=', today())
        ->orderBy('check_in_date')
        ->with(['guest', 'accommodation.property'])
        ->limit(5)
        ->get();
        
        // Counts
        $upcomingBookingsThisWeek = Reservation::whereHas('accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'confirmed')
        ->whereBetween('check_in_date', [today(), today()->addWeek()])
        ->count();
        
        $upcomingBookingsThisMonth = Reservation::whereHas('accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'confirmed')
        ->whereBetween('check_in_date', [today(), today()->addMonth()])
        ->count();
        
        // Top Partner
        $topB2bPartner = B2bPartner::whereHas('reservations.accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->withCount('reservations')
        ->orderBy('reservations_count', 'desc')
        ->first();
        
        // Recent Bookings
        $recentBookings = Reservation::whereHas('accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->with(['guest', 'accommodation.property'])
        ->latest()
        ->limit(5)
        ->get();
        
        // Pending Bookings
        $pendingBookings = Reservation::whereHas('accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'pending')
        ->with(['guest', 'accommodation.property'])
        ->latest()
        ->get();
        
        // Active Bookings
        $activeBookings = Reservation::whereHas('accommodation.property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->whereIn('status', ['confirmed', 'checked_in'])
        ->with(['guest', 'accommodation.property'])
        ->latest()
        ->get();

        $totalProperties = $properties->count();
        $pendingBookingsCount = $pendingBookings->count();
        $activeBookingsCount = $activeBookings->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties,
                'nextBooking' => $nextBookings->first(),
                'nextBookings' => $nextBookings,
                'stats' => [
                    'upcoming_week' => $upcomingBookingsThisWeek,
                    'upcoming_month' => $upcomingBookingsThisMonth,
                    'pending_bookings' => $pendingBookingsCount,
                    'active_bookings' => $activeBookingsCount,
                    'total_properties' => $totalProperties,
                ],
                'topB2bPartner' => $topB2bPartner,
                'recentBookings' => $recentBookings,
                'pendingBookings' => $pendingBookings,
                'activeBookings' => $activeBookings,
            ]
        ]);
    }
}
