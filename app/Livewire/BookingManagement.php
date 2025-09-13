<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\Reservation;

class BookingManagement extends Component
{
    public $selectedProperty = null;
    public $selectedBooking = null;
    public $view = 'list';

    protected $listeners = [
        'booking-created' => 'handleBookingCreated',
        'open-booking' => 'openBookingDetails',
    ];

    public function mount()
    {
        $this->selectedProperty = auth()->user()->properties()->first()?->id;
    }

    public function openBookingModal()
    {
        $this->dispatch('open-booking-modal');
    }

    public function handleBookingCreated()
    {
        $this->dispatch('$refresh');
    }

    public function refreshData()
    {
        $this->dispatch('$refresh');
    }

    public function openBookingDetails($data)
    {
        $bookingId = is_array($data) ? $data['bookingId'] : $data;
        $this->selectedBooking = Reservation::find($bookingId);
    }

    public function toggleBookingStatus($bookingId)
    {
        $booking = Reservation::with('accommodation.property')->find($bookingId);
        
        if (!$booking || $booking->accommodation->property->owner_id !== auth()->id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $newStatus = $booking->status === 'pending' ? 'confirmed' : 'pending';
        $booking->update(['status' => $newStatus]);
        
        session()->flash('success', 'Booking status updated successfully.');
    }

    public function getProperties()
    {
        return auth()->user()->properties;
    }

    public function getPendingBookings()
    {
        $query = Reservation::with(['guest', 'accommodation.property', 'b2bPartner'])
            ->where('status', 'pending');
        
        if ($this->selectedProperty) {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->selectedProperty);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        return $query->latest()->get();
    }

    public function getActiveBookings()
    {
        $query = Reservation::with(['guest', 'accommodation.property', 'b2bPartner'])
            ->whereIn('status', ['confirmed', 'checked_in']);
        
        if ($this->selectedProperty) {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->selectedProperty);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        return $query->latest()->get();
    }

    public function render()
    {
        return view('livewire.booking-management', [
            'properties' => $this->getProperties(),
            'pendingBookings' => $this->getPendingBookings(),
            'activeBookings' => $this->getActiveBookings(),
        ]);
    }
}