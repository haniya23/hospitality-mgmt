<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\Reservation;
use App\Models\CancelledBooking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['guest', 'accommodation.property', 'b2bPartner'])
            ->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });

        if ($request->property_id) {
            $query->whereHas('accommodation', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        $bookings = $query->latest()->get();

        return response()->json([
            'pending' => $bookings->where('status', 'pending')->values(),
            'active' => $bookings->whereIn('status', ['confirmed', 'checked_in'])->values()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'accommodation_id' => 'required|exists:property_accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'booking_type' => 'required|in:per_day,per_person',
            'guest_name' => 'nullable|string',
            'guest_mobile' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'b2b_partner_id' => 'nullable|exists:b2b_partners,uuid',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'use_b2b_reserved_customer' => 'nullable|in:0,1,true,false'
        ]);

        try {
            // Convert string boolean to actual boolean
            $useB2BReservedCustomer = filter_var($validated['use_b2b_reserved_customer'], FILTER_VALIDATE_BOOLEAN);
            
            // Handle B2B reserved customer
            if ($useB2BReservedCustomer && $validated['b2b_partner_id']) {
                $partner = \App\Models\B2bPartner::where('uuid', $validated['b2b_partner_id'])->first();
                $guest = $partner->getOrCreateReservedCustomer();
            } else {
                // Regular customer creation
                $guest = Guest::firstOrCreate(
                    ['mobile_number' => $request->guest_mobile],
                    [
                        'name' => $request->guest_name,
                        'email' => $request->guest_email
                    ]
                );
            }

            $booking = Reservation::create([
                'guest_id' => $guest->id,
                'property_accommodation_id' => $validated['accommodation_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'],
                'booking_type' => $validated['booking_type'],
                'total_amount' => $validated['total_amount'],
                'advance_paid' => $validated['advance_paid'],
                'balance_pending' => $validated['total_amount'] - $validated['advance_paid'],
                'special_requests' => $validated['special_requests'] ?? null,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'b2b_partner_id' => $partner ? $partner->id : null
            ]);

            // Create commission if B2B booking
            if ($partner) {
                $total = (float) $booking->total_amount;
                $pct = array_key_exists('commission_percentage', $validated) && $validated['commission_percentage'] !== null
                    ? (float) $validated['commission_percentage'] : 10.0; // default 10%
                $amt = array_key_exists('commission_amount', $validated) && $validated['commission_amount'] !== null
                    ? (float) $validated['commission_amount'] : null;

                if (is_null($amt)) {
                    $amt = round(($total * $pct) / 100, 2);
                } elseif (!$request->filled('commission_percentage')) {
                    $pct = $total > 0 ? round(($amt / $total) * 100, 2) : 0.0;
                }

                \App\Models\Commission::create([
                    'booking_id' => $booking->id,
                    'partner_id' => $partner->id,
                    'percentage' => $pct,
                    'amount' => $amt,
                    'status' => 'pending',
                ]);
            }

            return redirect()->route('bookings.index')
                ->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function toggleStatus($id)
    {
        try {
            $booking = Reservation::with('accommodation.property')->findOrFail($id);
            
            if ($booking->accommodation->property->owner_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $newStatus = $booking->status === 'pending' ? 'confirmed' : 'pending';
            $booking->update(['status' => $newStatus]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        try {
            $booking = Reservation::with('accommodation.property')->findOrFail($id);
            
            if ($booking->accommodation->property->owner_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            CancelledBooking::create([
                'reservation_id' => $booking->id,
                'reason' => $request->reason,
                'description' => $request->description,
                'refund_amount' => 0,
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now()
            ]);

            $booking->update(['status' => 'cancelled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getProperties()
    {
        $properties = Property::where('owner_id', auth()->id())->get(['id', 'name']);
        return response()->json($properties);
    }

    public function getAccommodations($propertyId)
    {
        try {
            $accommodations = PropertyAccommodation::with('predefinedType')
                ->where('property_id', $propertyId)
                ->get()
                ->map(function($acc) {
                    return [
                        'id' => $acc->id,
                        'custom_name' => $acc->custom_name,
                        'base_price' => $acc->base_price,
                        'max_occupancy' => $acc->max_occupancy,
                        'predefined_type' => [
                            'name' => $acc->predefinedType->name ?? 'Custom'
                        ]
                    ];
                });
            return response()->json($accommodations);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getGuests()
    {
        $guests = Guest::regularCustomers()
            ->orderBy('name')
            ->get(['id', 'name', 'mobile_number', 'email']);
        return response()->json($guests);
    }

    public function getPartners()
    {
        $partners = \App\Models\B2bPartner::where('status', 'active')
            ->get(['id', 'uuid', 'partner_name', 'commission_rate']);
        return response()->json($partners);
    }

    public function getPartnerReservedCustomer($partnerId)
    {
        $partner = \App\Models\B2bPartner::where('uuid', $partnerId)->first();
        
        if (!$partner) {
            return response()->json(['error' => 'Partner not found'], 404);
        }

        $reservedCustomer = $partner->reservedCustomer;
        
        if (!$reservedCustomer) {
            return response()->json(['error' => 'Reserved customer not found'], 404);
        }

        return response()->json([
            'id' => $reservedCustomer->id,
            'name' => $reservedCustomer->name,
            'email' => $reservedCustomer->email,
            'mobile_number' => $reservedCustomer->mobile_number
        ]);
    }

    public function create()
    {
        $properties = Property::where('owner_id', auth()->id())->get(['id', 'name']);
        return view('bookings.create', compact('properties'));
    }

    public function edit(Reservation $booking)
    {
        $booking->load(['guest', 'accommodation.property', 'b2bPartner']);
        
        if ($booking->accommodation->property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $properties = Property::where('owner_id', auth()->id())->get(['id', 'name']);
        $accommodations = PropertyAccommodation::where('property_id', $booking->accommodation->property_id)
            ->get(['id', 'custom_name', 'base_price']);
        
        return view('bookings.edit', compact('booking', 'properties', 'accommodations'));
    }

    public function update(Request $request, Reservation $booking)
    {
        $booking->load(['guest', 'accommodation.property']);
        
        if ($booking->accommodation->property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'accommodation_id' => 'required|exists:property_accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'guest_name' => 'required|string',
            'guest_mobile' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled'
        ]);

        try {
            $guest = Guest::firstOrCreate(
                ['mobile_number' => $request->guest_mobile],
                [
                    'name' => $request->guest_name,
                    'email' => $request->guest_email
                ]
            );

            $booking->update([
                'guest_id' => $guest->id,
                'property_accommodation_id' => $request->accommodation_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'adults' => $request->adults,
                'children' => $request->children,
                'total_amount' => $request->total_amount,
                'advance_paid' => $request->advance_paid,
                'balance_pending' => $request->total_amount - $request->advance_paid,
                'status' => $request->status
            ]);

            return redirect()->route('bookings.index')
                ->with('success', 'Booking updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function getAccommodationCount()
    {
        $properties = Property::where('owner_id', auth()->id())
            ->withCount('propertyAccommodations')
            ->get();
        
        $totalProperties = $properties->count();
        $totalAccommodations = $properties->sum('property_accommodations_count');
        
        $response = [
            'totalProperties' => $totalProperties,
            'totalAccommodations' => $totalAccommodations
        ];
        
        // If only one property with one accommodation, provide default values
        if ($totalProperties === 1 && $totalAccommodations === 1) {
            $property = $properties->first();
            $accommodation = $property->propertyAccommodations->first();
            
            $response['defaultPropertyId'] = $property->id;
            $response['defaultAccommodationId'] = $accommodation->id;
            $response['defaultPrice'] = $accommodation->base_price;
            $response['defaultAccommodation'] = [
                'id' => $accommodation->id,
                'display_name' => $accommodation->display_name,
                'base_price' => $accommodation->base_price,
                'max_occupancy' => $accommodation->max_occupancy,
                'property_name' => $property->name,
                'predefined_type' => [
                    'name' => $accommodation->predefinedType->name ?? 'Custom'
                ]
            ];
        }
        
        return response()->json($response);
    }

    public function cancelled(Request $request)
    {
        $query = CancelledBooking::with(['reservation.guest', 'reservation.accommodation.property', 'reservation.b2bPartner'])
            ->whereHas('reservation.accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });

        if ($request->property_id) {
            $query->whereHas('reservation.accommodation', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        $cancelledBookings = $query->latest()->paginate(10);

        $properties = Property::where('owner_id', auth()->id())
            ->get(['id', 'name']);

        return view('bookings.cancelled', compact('cancelledBookings', 'properties'));
    }
}