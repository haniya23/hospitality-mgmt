<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Models\PropertyAccommodation;
use App\Models\B2bPartner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function simple()
    {
        // Fetch properties with relationships
        $properties = Property::with([
            'category',
            'location.city.district.state.country',
            'propertyAccommodations' => function($query) {
                $query->where('is_active', true);
            },
            'photos',
            'amenities'
        ])
        ->where('status', 'active')
        ->latest()
        ->limit(6)
        ->get();

        // Get booking statistics
        $totalBookings = Reservation::count();
        $todayBookings = Reservation::whereDate('created_at', Carbon::today())->count();
        
        // Get total revenue
        $totalRevenue = Reservation::where('status', 'confirmed')
            ->sum(DB::raw('total_amount'));
        
        // Get active users count
        $activeUsers = User::where('is_active', true)->count();
        
        // Get total properties
        $totalProperties = Property::where('status', 'active')->count();
        
        // Get top 10 travel partners by booking count
        $topTravelPartners = B2bPartner::select('b2b_partners.*')
            ->selectSub(
                DB::table('reservations')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('reservations.b2b_partner_id', 'b2b_partners.id')
                    ->where('reservations.status', 'confirmed'),
                'booking_count'
            )
            ->where('status', 'active')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get();

        return view('public.simple', compact(
            'properties',
            'totalBookings',
            'todayBookings',
            'totalRevenue',
            'activeUsers',
            'totalProperties',
            'topTravelPartners'
        ));
    }
}
