<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\CancelledBooking;

class BookingManagement extends Component
{
    use WithPagination;
    public $selectedProperty = null;
    public $selectedBooking = null;
    public $view = 'list';
    public $showCancelModal = false;
    public $cancelBookingId = null;
    public $cancelReason = '';
    public $cancelDescription = '';
    public $showCancelledBookings = false;

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

    public function openCancelModal($bookingId)
    {
        $this->cancelBookingId = $bookingId;
        $this->showCancelModal = true;
        $this->cancelReason = '';
        $this->cancelDescription = '';
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelBookingId = null;
        $this->cancelReason = '';
        $this->cancelDescription = '';
    }

    public function cancelBooking()
    {
        if (!$this->cancelReason) {
            session()->flash('error', 'Please select a reason for cancellation.');
            return;
        }

        $booking = Reservation::with('accommodation.property')->find($this->cancelBookingId);
        
        if (!$booking || $booking->accommodation->property->owner_id !== auth()->id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        CancelledBooking::create([
            'reservation_id' => $booking->id,
            'reason' => $this->cancelReason,
            'description' => $this->cancelDescription,
            'refund_amount' => 0,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        $booking->update(['status' => 'cancelled']);
        
        session()->flash('success', 'Booking cancelled successfully.');
        $this->closeCancelModal();
    }

    public function getCancelledBookings()
    {
        $query = Reservation::with(['guest', 'accommodation.property', 'cancelledBooking'])
            ->where('status', 'cancelled');
        
        if ($this->selectedProperty) {
            $query->whereHas('accommodation', function($q) {
                $q->where('property_id', $this->selectedProperty);
            });
        } else {
            $query->whereHas('accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            });
        }

        return $query->latest()->paginate(5, ['*'], 'cancelled');
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

        return $query->latest()->paginate(10, ['*'], 'pending');
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

        return $query->latest()->paginate(10, ['*'], 'active');
    }

    public function render()
    {
        return view('livewire.booking-management', [
            'properties' => $this->getProperties(),
            'pendingBookings' => $this->getPendingBookings(),
            'activeBookings' => $this->getActiveBookings(),
            'cancelledBookings' => $this->getCancelledBookings(),
        ]);
    }
}