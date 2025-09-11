<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\Reservation;

class BookingManagement extends Component
{
    public $selectedProperty = null;
    public $showBookingModal = false;
    public $selectedBooking = null;
    public $view = 'calendar'; // 'calendar' or 'list'

    protected $listeners = [
        'booking-created' => 'handleBookingCreated',
        'date-selected' => 'handleDateSelection',
        'open-booking' => 'openBookingDetails',
    ];

    public function mount()
    {
        $this->selectedProperty = auth()->user()->properties()->first()?->id;
    }

    public function switchView($view)
    {
        $this->view = $view;
    }

    public function openBookingModal()
    {
        $this->dispatch('open-booking-modal');
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
    }

    public function handleBookingCreated()
    {
        $this->closeBookingModal();
        $this->dispatch('$refresh');
    }

    public function refreshData()
    {
        $this->dispatch('$refresh');
    }

    public function handleDateSelection($data)
    {
        // Handle date selection from calendar
        $this->openBookingModal();
    }

    public function openBookingDetails($data)
    {
        $this->selectedBooking = Reservation::find($data['bookingId']);
        // Could open a booking details modal here
    }

    public function getProperties()
    {
        return auth()->user()->properties;
    }

    public function getRecentBookings()
    {
        $query = Reservation::with(['guest', 'accommodation.property', 'b2bPartner']);
        
        if ($this->selectedProperty) {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->selectedProperty);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        return $query->latest()->take(10)->get();
    }

    public function render()
    {
        return view('livewire.booking-management', [
            'properties' => $this->getProperties(),
            'recentBookings' => $this->getRecentBookings(),
        ]);
    }
}