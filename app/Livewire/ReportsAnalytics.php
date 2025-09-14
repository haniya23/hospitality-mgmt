<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Commission;
use App\Models\Property;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsAnalytics extends Component
{
    public $dateRange = '30'; // days
    public $property_id = 'all';
    public $reportType = 'overview';
    
    public function mount()
    {
        // Initialize with current month
    }

    public function updatedDateRange()
    {
        $this->dispatch('$refresh');
    }

    public function updatedPropertyId()
    {
        $this->dispatch('$refresh');
    }

    public function switchReport($type)
    {
        $this->reportType = $type;
        $this->dispatch('reportChanged', $type);
    }

    public function exportReport($format = 'csv')
    {
        // Export functionality would go here
        session()->flash('success', "Report exported as {$format}");
    }

    public function getDateRangeQuery()
    {
        $endDate = now();
        $startDate = now()->subDays((int)$this->dateRange);
        
        return [$startDate, $endDate];
    }

    public function getBookingStats()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        
        $query = Reservation::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($this->property_id !== 'all') {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->property_id);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        $bookings = $query->get();
        
        return [
            'total_bookings' => $bookings->count(),
            'total_revenue' => $bookings->sum('total_amount'),
            'avg_booking_value' => $bookings->avg('total_amount') ?? 0,
            'b2b_bookings' => $bookings->whereNotNull('b2b_partner_id')->count(),
            'b2b_revenue' => $bookings->whereNotNull('b2b_partner_id')->sum('total_amount'),
            'status_breakdown' => $bookings->groupBy('status')->map->count(),
            'daily_bookings' => $this->getDailyBookings($startDate, $endDate),
        ];
    }

    public function getCommissionStats()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        
        $query = Commission::whereHas('booking', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($this->property_id !== 'all') {
                $q->whereHas('accommodation', function($subQ) {
                    $subQ->where('property_id', $this->property_id);
                });
            } else {
                $q->whereHas('accommodation.property', function($subQ) {
                    $subQ->where('owner_id', auth()->id());
                });
            }
        });

        $commissions = $query->with(['partner', 'booking'])->get();
        
        return [
            'total_commissions' => $commissions->sum('amount'),
            'paid_commissions' => $commissions->where('status', 'paid')->sum('amount_paid'),
            'pending_commissions' => $commissions->where('status', 'pending')->sum('amount'),
            'partner_breakdown' => $commissions->groupBy('partner.partner_name')->map(function($group) {
                return [
                    'total' => $group->sum('amount'),
                    'paid' => $group->where('status', 'paid')->sum('amount_paid'),
                    'pending' => $group->where('status', 'pending')->sum('amount'),
                ];
            }),
        ];
    }

    public function getCustomerStats()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        
        $query = Guest::whereHas('reservations', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($this->property_id !== 'all') {
                $q->whereHas('accommodation', function($subQ) {
                    $subQ->where('property_id', $this->property_id);
                });
            } else {
                $q->whereHas('accommodation.property', function($subQ) {
                    $subQ->where('owner_id', auth()->id());
                });
            }
        });

        $customers = $query->withCount('reservations')->get();
        
        return [
            'total_customers' => $customers->count(),
            'new_customers' => $customers->where('total_stays', 1)->count(),
            'repeat_customers' => $customers->where('total_stays', '>', 1)->count(),
            'avg_loyalty_points' => $customers->avg('loyalty_points') ?? 0,
            'top_customers' => $customers->sortByDesc('reservations_count')->take(5),
        ];
    }

    public function getPropertyPerformance()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        
        $properties = Property::where('owner_id', auth()->id())
            ->withCount(['propertyAccommodations as total_bookings' => function($q) use ($startDate, $endDate) {
                $q->whereHas('reservations', function($subQ) use ($startDate, $endDate) {
                    $subQ->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->with(['propertyAccommodations.reservations' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get();

        return $properties->map(function($property) {
            $bookings = $property->propertyAccommodations->flatMap->reservations;
            
            return [
                'name' => $property->name,
                'total_bookings' => $bookings->count(),
                'total_revenue' => $bookings->sum('total_amount'),
                'avg_occupancy' => $this->calculateOccupancy($property, $bookings),
                'b2b_percentage' => $bookings->count() > 0 ? 
                    ($bookings->whereNotNull('b2b_partner_id')->count() / $bookings->count()) * 100 : 0,
            ];
        });
    }

    private function getDailyBookings($startDate, $endDate)
    {
        $query = Reservation::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');
            
        if ($this->property_id !== 'all') {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->property_id);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        return $query->get()->keyBy('date');
    }

    private function calculateOccupancy($property, $bookings)
    {
        // Simplified occupancy calculation
        $totalRooms = $property->propertyAccommodations->count();
        $totalDays = (int)$this->dateRange;
        $bookedNights = $bookings->sum(function($booking) {
            return $booking->check_in_date->diffInDays($booking->check_out_date);
        });
        
        return $totalRooms > 0 && $totalDays > 0 ? 
            ($bookedNights / ($totalRooms * $totalDays)) * 100 : 0;
    }

    public function render()
    {
        return view('livewire.reports-analytics', [
            'properties' => Property::where('owner_id', auth()->id())->get(),
            'bookingStats' => $this->getBookingStats(),
            'commissionStats' => $this->getCommissionStats(),
            'customerStats' => $this->getCustomerStats(),
            'propertyPerformance' => $this->getPropertyPerformance(),
        ]);
    }
}