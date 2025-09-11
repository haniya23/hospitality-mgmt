<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;

class BookingCalendar extends Component
{
    public $property_id;
    public $currentMonth;
    public $currentYear;
    public $bookings = [];
    public $selectedDate = null;
    
    public function mount($propertyId = null)
    {
        $this->property_id = $propertyId;
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->loadBookings();
    }

    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->loadBookings();
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->loadBookings();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->dispatch('date-selected', ['date' => $date]);
    }

    public function openBooking($bookingId)
    {
        $this->dispatch('open-booking', ['bookingId' => $bookingId]);
    }

    private function loadBookings()
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $query = Reservation::whereBetween('check_in_date', [$startDate, $endDate])
            ->orWhereBetween('check_out_date', [$startDate, $endDate])
            ->orWhere(function($q) use ($startDate, $endDate) {
                $q->where('check_in_date', '<=', $startDate)
                  ->where('check_out_date', '>=', $endDate);
            });

        if ($this->property_id) {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->property_id);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        $this->bookings = $query->with(['guest', 'accommodation', 'b2bPartner'])->get()
            ->groupBy(function($booking) {
                return $booking->check_in_date->format('Y-m-d');
            });
    }

    public function getCalendarDays()
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Start from the beginning of the week
        $calendarStart = $startDate->copy()->startOfWeek();
        $calendarEnd = $endDate->copy()->endOfWeek();
        
        $days = [];
        $current = $calendarStart->copy();
        
        while ($current <= $calendarEnd) {
            $dateStr = $current->format('Y-m-d');
            $dayBookings = $this->bookings[$dateStr] ?? collect();
            
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month == $this->currentMonth,
                'isToday' => $current->isToday(),
                'bookings' => $dayBookings,
                'bookingCount' => $dayBookings->count(),
                'hasCheckIn' => $dayBookings->where('check_in_date', $dateStr)->count() > 0,
                'hasCheckOut' => $dayBookings->where('check_out_date', $dateStr)->count() > 0,
            ];
            
            $current->addDay();
        }
        
        return collect($days)->chunk(7);
    }

    public function render()
    {
        return view('livewire.booking-calendar', [
            'calendarWeeks' => $this->getCalendarDays(),
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y')
        ]);
    }
}