<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\PropertyAccommodation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get staff assignments to determine which properties they can see
        $staffAssignments = $user->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();
        
        // Get upcoming bookings (next 30 days) for assigned properties
        $upcomingBookings = Reservation::with([
                'guest', 
                'accommodation.property', 
                'b2bPartner',
                'checkInRecord',
                'checkOutRecord'
            ])
            ->whereHas('accommodation.property', function($query) use ($propertyIds) {
                $query->whereIn('id', $propertyIds);
            })
            ->where('check_in_date', '>=', now())
            ->where('check_in_date', '<=', now()->addDays(30))
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        // Group bookings by date
        $bookingsByDate = $upcomingBookings->groupBy(function($booking) {
            return Carbon::parse($booking->check_in_date)->format('Y-m-d');
        });

        // Get today's check-ins
        $todaysCheckIns = Reservation::with([
                'guest', 
                'accommodation.property', 
                'b2bPartner',
                'checkInRecord'
            ])
            ->whereHas('accommodation.property', function($query) use ($propertyIds) {
                $query->whereIn('id', $propertyIds);
            })
            ->where('check_in_date', today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        // Get today's check-outs
        $todaysCheckOuts = Reservation::with([
                'guest', 
                'accommodation.property', 
                'b2bPartner',
                'checkOutRecord'
            ])
            ->whereHas('accommodation.property', function($query) use ($propertyIds) {
                $query->whereIn('id', $propertyIds);
            })
            ->where('check_out_date', today())
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('check_out_date', 'asc')
            ->get();

        return view('staff.bookings.index', compact(
            'upcomingBookings', 
            'bookingsByDate', 
            'todaysCheckIns', 
            'todaysCheckOuts'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Check if staff has booking access
        $staffAssignments = $user->getActiveStaffAssignments();
        $hasBookingAccess = $staffAssignments->where('booking_access', true)->count() > 0;
        
        if (!$hasBookingAccess) {
            abort(403, 'You do not have permission to create bookings.');
        }
        
        // Get staff assignments to determine which properties they can see
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();

        // Get properties assigned to this staff member
        $properties = \App\Models\Property::whereIn('id', $propertyIds)->get();
        
        // Get accommodations for these properties
        $accommodations = \App\Models\PropertyAccommodation::whereIn('property_id', $propertyIds)->get();
        
        // Get B2B partners for these properties
        $b2bPartners = \App\Models\B2bPartner::whereIn('property_id', $propertyIds)->get();

        return view('staff.bookings.create', compact('properties', 'accommodations', 'b2bPartners'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check if staff has booking access
        $staffAssignments = $user->getActiveStaffAssignments();
        $hasBookingAccess = $staffAssignments->where('booking_access', true)->count() > 0;
        
        if (!$hasBookingAccess) {
            abort(403, 'You do not have permission to create bookings.');
        }
        
        // Get staff assignments to determine which properties they can see
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'accommodation_id' => 'required|exists:property_accommodations,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'booking_type' => 'required|in:per_day,per_person',
            'guest_name' => 'required|string|max:255',
            'guest_mobile' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0|lte:total_amount',
            'special_requests' => 'nullable|string|max:1000',
            'b2b_partner_id' => 'nullable|exists:b2b_partners,uuid',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'use_b2b_reserved_customer' => 'nullable|in:0,1,true,false',
            'use_accommodation_reserved_customer' => 'nullable|in:0,1,true,false'
        ]);

        // Verify staff has access to the selected property
        if (!in_array($request->property_id, $propertyIds)) {
            return redirect()->back()->withErrors(['property_id' => 'You do not have access to this property.']);
        }

        try {
            // Initialize partner variable
            $partner = null;
            
            // Convert string booleans to actual booleans
            $useB2BReservedCustomer = filter_var($request->use_b2b_reserved_customer, FILTER_VALIDATE_BOOLEAN);
            $useAccommodationReservedCustomer = filter_var($request->use_accommodation_reserved_customer ?? false, FILTER_VALIDATE_BOOLEAN);
            
            // Handle reserved customers
            if ($useB2BReservedCustomer && $request->b2b_partner_id) {
                // B2B reserved customer
                $partner = \App\Models\B2bPartner::where('uuid', $request->b2b_partner_id)->first();
                $guest = $partner->getOrCreateReservedCustomer();
            } elseif ($useAccommodationReservedCustomer && $request->accommodation_id) {
                // Accommodation reserved customer
                $accommodation = \App\Models\PropertyAccommodation::find($request->accommodation_id);
                $guest = $accommodation->getOrCreateReservedCustomer();
            } else {
                // Regular customer creation
                $guest = \App\Models\Guest::firstOrCreate(
                    ['mobile_number' => $request->guest_mobile],
                    [
                        'name' => $request->guest_name,
                        'email' => $request->guest_email
                    ]
                );
            }

            $booking = \App\Models\Reservation::create([
                'guest_id' => $guest->id,
                'property_accommodation_id' => $request->accommodation_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'adults' => $request->adults,
                'children' => $request->children,
                'booking_type' => $request->booking_type,
                'total_amount' => $request->total_amount,
                'advance_paid' => $request->advance_paid,
                'balance_pending' => $request->total_amount - $request->advance_paid,
                'special_requests' => $request->special_requests ?? null,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'b2b_partner_id' => $partner ? $partner->id : null
            ]);

            // Create commission if B2B booking
            if ($partner) {
                $total = (float) $booking->total_amount;
                $pct = $request->commission_percentage ?? 10.0; // default 10%
                $amt = $request->commission_amount ?? null;

                if (is_null($amt)) {
                    $amt = round(($total * $pct) / 100, 2);
                }

                \App\Models\Commission::create([
                    'reservation_id' => $booking->id,
                    'b2b_partner_id' => $partner->id,
                    'commission_percentage' => $pct,
                    'commission_amount' => $amt,
                    'status' => 'pending'
                ]);
            }

            return redirect()->route('staff.bookings')->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
        }
    }

    public function updateCustomerDetails(Request $request, Reservation $booking)
    {
        $user = auth()->user();
        
        // Check if staff has booking access
        $staffAssignments = $user->getActiveStaffAssignments();
        $hasBookingAccess = $staffAssignments->where('booking_access', true)->count() > 0;
        
        if (!$hasBookingAccess) {
            abort(403, 'You do not have permission to edit bookings.');
        }
        
        // Check if staff has access to this booking's property
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();
        
        if (!in_array($booking->accommodation->property_id, $propertyIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_id_type' => 'nullable|string|max:50',
            'customer_id_number' => 'nullable|string|max:100',
            'customer_address' => 'nullable|string|max:500',
        ]);

        try {
            // Handle B2B bookings differently
            if ($booking->isB2bBooking()) {
                // For B2B bookings, update the reserved customer details
                $reservedCustomer = $booking->b2bPartner->getOrCreateReservedCustomer();
                
                $reservedCustomer->update([
                    'name' => $request->customer_name,
                    'mobile_number' => $request->customer_mobile,
                    'email' => $request->customer_email,
                    'id_type' => $request->customer_id_type,
                    'id_number' => $request->customer_id_number,
                    'address' => $request->customer_address,
                ]);
            } else {
                // For regular bookings, update the guest details
                $booking->guest->update([
                    'name' => $request->customer_name,
                    'mobile_number' => $request->customer_mobile,
                    'email' => $request->customer_email,
                    'id_type' => $request->customer_id_type,
                    'id_number' => $request->customer_id_number,
                    'address' => $request->customer_address,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer details updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerDetails(Reservation $booking)
    {
        $user = auth()->user();
        
        // Check if staff has access to this booking's property (all staff can view)
        $staffAssignments = $user->getActiveStaffAssignments();
        $propertyIds = $staffAssignments->pluck('property_id')->toArray();
        
        if (!in_array($booking->accommodation->property_id, $propertyIds)) {
            abort(403, 'You do not have access to this booking.');
        }
        
        // Check if staff has booking access (for editing)
        $hasBookingAccess = $staffAssignments->where('booking_access', true)->count() > 0;

        // Handle B2B bookings
        if ($booking->isB2bBooking()) {
            $customer = $booking->b2bPartner->getOrCreateReservedCustomer();
            $isB2bBooking = true;
            $b2bPartnerName = $booking->b2bPartner->partner_name;
        } else {
            $customer = $booking->guest;
            $isB2bBooking = false;
            $b2bPartnerName = null;
        }

        return response()->json([
            'customer' => [
                'name' => $customer->name,
                'mobile_number' => $customer->mobile_number,
                'email' => $customer->email,
                'id_type' => $customer->id_type,
                'id_number' => $customer->id_number,
                'address' => $customer->address,
            ],
            'is_b2b_booking' => $isB2bBooking,
            'b2b_partner_name' => $b2bPartnerName,
            'booking_type' => $isB2bBooking ? 'B2B Booking' : 'Regular Booking',
            'can_edit' => $hasBookingAccess
        ]);
    }
}
