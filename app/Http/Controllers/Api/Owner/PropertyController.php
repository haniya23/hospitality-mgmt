<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Get owner properties list
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $properties = $user->properties()
            ->with([
                'category', 
                'location.city.district.state', 
                'photos',
                'amenities',
                'propertyAccommodations.reservedCustomer', 
                'propertyAccommodations.predefinedType',
                'propertyAccommodations.photos',
                'propertyAccommodations.amenities'
            ])
            ->withCount('propertyAccommodations')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties
        ]);
    }
    /**
     * Update property details
     */
    public function update(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $property->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully',
            'data' => $property
        ]);
    }

    /**
     * Toggle property status
     */
    public function toggleStatus(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => 'Property status updated to ' . $newStatus,
            'data' => $property
        ]);
    }
    /**
     * Store property photos
     */
    public function storePhotos(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('property-photos', 'public');
                $property->photos()->create([
                    'file_path' => $path,
                    'is_main' => $property->photos()->doesntExist(), // First photo is main
                    'file_size' => $photo->getSize(),
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Photos uploaded successfully',
            'data' => $property->load('photos')
        ]);
    }
    
    /**
     * Delete property photo
     */
    public function deletePhoto(Request $request, $id, $photoId)
    {
        $property = $request->user()->properties()->findOrFail($id);
        $photo = $property->photos()->where('id', $photoId)->firstOrFail();
        
        // Delete from storage
        if (\Storage::disk('public')->exists($photo->file_path)) {
            \Storage::disk('public')->delete($photo->file_path);
        }
        
        $photo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully'
        ]);
    }

    /**
     * Store accommodation photos
     */
    public function storeAccommodationPhotos(Request $request, $id, $accommodationId)
    {
        $property = $request->user()->properties()->findOrFail($id);
        $accommodation = $property->propertyAccommodations()->findOrFail($accommodationId);
        
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('accommodations', 'public');
                $accommodation->photos()->create([
                    'file_path' => $path,
                    'is_main' => $accommodation->photos()->doesntExist(),
                    'file_size' => $photo->getSize(),
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Photos uploaded successfully',
            'data' => $accommodation->load('photos')
        ]);
    }
    
    /**
     * Delete accommodation photo
     */
    public function deleteAccommodationPhoto(Request $request, $id, $accommodationId, $photoId)
    {
        $property = $request->user()->properties()->findOrFail($id);
        $accommodation = $property->propertyAccommodations()->findOrFail($accommodationId);
        $photo = $accommodation->photos()->where('id', $photoId)->firstOrFail();
        
        if (\Storage::disk('public')->exists($photo->file_path)) {
            \Storage::disk('public')->delete($photo->file_path);
        }
        
        $photo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully'
        ]);
    }
    /**
     * Get comprehensive property details for dashboard
     */
    public function show(Request $request, $id)
    {
        $property = $request->user()->properties()->findOrFail($id);
        
        // Load all necessary relationships
        $property->load([
            'category',
            'location.city.district.state',
            'propertyAccommodations.reservations.guest',
            'propertyAccommodations.reservations.b2bPartner',
            'staffMembers.user',
            'staffMembers.department',
            'tasks' => function($query) {
                $query->whereDate('scheduled_at', today())->orWhere('status', 'pending');
            }
        ]);

        // Get today's data
        $today = today();
        
        // Current guests (checked in)
        $currentGuests = $property->reservations()
            ->where('status', 'checked_in')
            ->with(['guest', 'propertyAccommodation'])
            ->get();

        // Next check-ins (next 24 hours)
        $nextCheckins = $property->reservations()
            ->where('status', 'confirmed')
            ->whereDate('check_in_date', '>=', $today)
            ->whereDate('check_in_date', '<=', $today->addDay())
            ->with(['guest', 'propertyAccommodation'])
            ->orderBy('check_in_date')
            ->limit(5)
            ->get();

        // Next check-outs (next 24 hours)
        $nextCheckouts = $property->reservations()
            ->where('status', 'checked_in')
            ->whereDate('check_out_date', '>=', $today)
            ->whereDate('check_out_date', '<=', $today->addDay())
            ->with(['guest', 'propertyAccommodation'])
            ->orderBy('check_out_date')
            ->limit(5)
            ->get();

        // Today's revenue
        $todaysRevenue = $property->reservations()
            ->whereDate('reservations.created_at', $today)
            ->sum('total_amount');

        // Occupancy stats
        $totalAccommodations = $property->propertyAccommodations()->count();
        $occupiedAccommodations = $property->propertyAccommodations()
            ->whereHas('reservations', function($query) use ($today) {
                $query->where('status', 'checked_in')
                      ->where('check_in_date', '<=', $today)
                      ->where('check_out_date', '>', $today);
            })
            ->count();

        // Staff on duty today
        $staffOnDuty = $property->staffMembers()
            ->where('status', 'active')
            ->with(['user', 'department'])
            ->get();

        // Pending tasks
        $pendingTasks = $property->tasks()
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['assignedStaff.user'])
            ->orderBy('scheduled_at')
            ->get();

        // Overdue tasks
        $overdueTasks = $property->tasks()
            ->where('scheduled_at', '<', now())
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['assignedStaff.user'])
            ->get();

        // Maintenance tickets
        $maintenanceTickets = \App\Models\MaintenanceTicket::whereHas('propertyAccommodation', function($query) use ($property) {
            $query->where('property_id', $property->id);
        })->whereIn('status', ['open', 'in_progress'])
        ->with(['propertyAccommodation', 'reportedBy', 'assignedTo'])
        ->get();

        // B2B partners with today's bookings
        $b2bPartners = $property->reservations()
            ->whereDate('reservations.created_at', $today)
            ->whereNotNull('b2b_partner_id')
            ->with('b2bPartner')
            ->get()
            ->groupBy('b2b_partner_id')
            ->map(function($bookings) {
                $partner = $bookings->first()->b2bPartner;
                return [
                    'partner' => $partner,
                    'bookings_count' => $bookings->count(),
                    'revenue' => $bookings->sum('total_amount')
                ];
            })->values(); // Reset keys for JSON array

        // Monthly stats
        $monthlyStats = [
            'occupancy_rate' => $this->calculateOccupancyRate($property, now()->startOfMonth(), now()->endOfMonth()),
            'revenue' => $property->reservations()
                ->whereBetween('reservations.created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_amount'),
            'total_bookings' => $property->reservations()
                ->whereBetween('reservations.created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'average_stay' => $this->calculateAverageStay($property, now()->startOfMonth(), now()->endOfMonth())
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'property' => $property,
                'stats' => [
                    'currentGuests' => $currentGuests,
                    'nextCheckins' => $nextCheckins,
                    'nextCheckouts' => $nextCheckouts,
                    'todaysRevenue' => $todaysRevenue,
                    'totalAccommodations' => $totalAccommodations,
                    'occupiedAccommodations' => $occupiedAccommodations,
                    'staffOnDuty' => $staffOnDuty,
                    'pendingTasks' => $pendingTasks,
                    'overdueTasks' => $overdueTasks,
                    'maintenanceTickets' => $maintenanceTickets,
                    'b2bPartners' => $b2bPartners,
                    'monthlyStats' => $monthlyStats
                ]
            ]
        ]);
    }

    private function calculateOccupancyRate($property, $startDate, $endDate)
    {
        $totalRoomNights = $property->propertyAccommodations()->count() * $startDate->diffInDays($endDate);
        if ($totalRoomNights == 0) return 0;

        $occupiedRoomNights = $property->reservations()
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->orWhereBetween('check_out_date', [$startDate, $endDate])
            ->sum(\DB::raw('DATEDIFF(check_out_date, check_in_date)'));
        
        return $totalRoomNights > 0 ? round(($occupiedRoomNights / $totalRoomNights) * 100, 2) : 0;
    }

    private function calculateAverageStay($property, $startDate, $endDate)
    {
        $bookings = $property->reservations()
            ->whereBetween('reservations.created_at', [$startDate, $endDate])
            ->get();
        
        if ($bookings->isEmpty()) {
            return 0;
        }
        
        $totalNights = $bookings->sum(function($booking) {
            return $booking->check_in_date->diffInDays($booking->check_out_date);
        });
        
        return round($totalNights / $bookings->count(), 1);
    }
}
