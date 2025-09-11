<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\B2bPartner;
use App\Models\B2bRequest;
use App\Models\Commission;
use App\Models\User;

class B2bDashboard extends Component
{
    public $activeTab = 'receiving'; // 'receiving' or 'sending'
    public $showRequestModal = false;
    public $selectedRequest = null;
    
    // Filters
    public $statusFilter = 'all';
    public $partnerFilter = 'all';
    
    public function mount()
    {
        // Default to receiving tab
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->statusFilter = 'all';
        $this->partnerFilter = 'all';
    }

    public function openRequestModal($requestId = null)
    {
        $this->selectedRequest = $requestId ? B2bRequest::find($requestId) : null;
        $this->showRequestModal = true;
    }

    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->selectedRequest = null;
    }

    public function acceptRequest($requestId)
    {
        $request = B2bRequest::find($requestId);
        if ($request) {
            $booking = $request->accept();
            session()->flash('success', 'Request accepted and booking created!');
            $this->dispatch('booking-created', ['booking' => $booking->id]);
        }
    }

    public function rejectRequest($requestId, $reason = null)
    {
        $request = B2bRequest::find($requestId);
        if ($request) {
            $request->reject($reason);
            session()->flash('success', 'Request rejected.');
        }
    }

    public function markCommissionPaid($commissionId, $amount)
    {
        $commission = Commission::find($commissionId);
        if ($commission) {
            $commission->markAsPaid($amount);
            session()->flash('success', 'Commission marked as paid.');
        }
    }

    // Get bookings received from B2B partners (as property owner)
    public function getReceivingBookings()
    {
        $query = Reservation::whereHas('accommodation.property', function($q) {
            $q->where('owner_id', auth()->id());
        })->whereNotNull('b2b_partner_id')
        ->with(['guest', 'accommodation.property', 'b2bPartner', 'commission']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->partnerFilter !== 'all') {
            $query->where('b2b_partner_id', $this->partnerFilter);
        }

        return $query->latest()->get();
    }

    // Get bookings sent to other properties (as B2B partner)
    public function getSendingBookings()
    {
        $partnerRecord = B2bPartner::where('contact_user_id', auth()->id())->first();
        
        if (!$partnerRecord) {
            return collect();
        }

        $query = Reservation::where('b2b_partner_id', $partnerRecord->id)
            ->with(['guest', 'accommodation.property', 'commission']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->get();
    }

    // Get incoming B2B requests (as property owner)
    public function getIncomingRequests()
    {
        return B2bRequest::whereHas('toProperty', function($q) {
            $q->where('owner_id', auth()->id());
        })->with(['fromPartner', 'toProperty', 'guest'])
        ->where('status', '!=', 'accepted')
        ->latest()->get();
    }

    // Get outgoing B2B requests (as partner)
    public function getOutgoingRequests()
    {
        return B2bRequest::where('from_partner_id', auth()->id())
            ->with(['toProperty', 'guest'])
            ->latest()->get();
    }

    // Get available partners for current user
    public function getAvailablePartners()
    {
        return B2bPartner::where('status', 'active')
            ->whereHas('contactUser.properties', function($q) {
                $q->where('owner_id', '!=', auth()->id());
            })->get();
    }

    // Get partner statistics
    public function getPartnerStats()
    {
        $receivingBookings = $this->getReceivingBookings();
        $sendingBookings = $this->getSendingBookings();
        
        return [
            'total_received' => $receivingBookings->count(),
            'total_sent' => $sendingBookings->count(),
            'pending_commissions' => Commission::whereHas('booking.accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            })->where('status', 'pending')->sum('amount'),
            'paid_commissions' => Commission::whereHas('booking.accommodation.property', function($q) {
                $q->where('owner_id', auth()->id());
            })->where('status', 'paid')->sum('amount_paid'),
            'receivable_commissions' => $this->getReceivableCommissions(),
        ];
    }

    private function getReceivableCommissions()
    {
        $partnerRecord = B2bPartner::where('contact_user_id', auth()->id())->first();
        
        if (!$partnerRecord) {
            return 0;
        }

        return Commission::where('partner_id', $partnerRecord->id)
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.b2b-dashboard', [
            'receivingBookings' => $this->getReceivingBookings(),
            'sendingBookings' => $this->getSendingBookings(),
            'incomingRequests' => $this->getIncomingRequests(),
            'outgoingRequests' => $this->getOutgoingRequests(),
            'availablePartners' => $this->getAvailablePartners(),
            'stats' => $this->getPartnerStats(),
        ]);
    }
}