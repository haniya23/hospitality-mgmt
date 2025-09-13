<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\Reservation;
use App\Services\BookingService;
use Carbon\Carbon;

class BookingModal extends Component
{
    public $isOpen = false;
    public $mode = 'quick'; // 'quick' or 'full'
    public $allow_past_dates = false;
    
    protected $listeners = [
        'open-booking-modal' => 'open',
    ];
    
    // Booking data
    public $property_id;
    public $accommodation_id;
    public $check_in_date;
    public $check_out_date;
    public $adults = 1;
    public $children = 0;
    public $guest_id;
    public $b2b_partner_id;
    public $total_amount = 0;
    public $advance_paid = 0;
    public $balance_pending = 0;
    public $rate_override;
    public $override_reason;
    public $special_requests;
    public $notes;
    
    // Guest creation
    public $guest_name;
    public $guest_mobile;
    public $guest_email;
    public $create_new_guest = false;
    
    // B2B partner creation
    public $partner_mobile;
    public $partner_name;
    public $create_new_partner = false;
    
    // Calculated values
    public $base_rate = 0;
    public $nights = 0;
    public $applicable_discounts = [];
    
    // Cached collections to prevent hydration issues
    public $properties = [];
    public $accommodations = [];
    public $guests = [];
    public $partners = [];

    protected function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'accommodation_id' => 'required|exists:property_accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'property_id.required' => 'Please select a property.',
        'accommodation_id.required' => 'Please select an accommodation.',
        'check_in_date.required' => 'Check-in date is required.',
        'check_out_date.required' => 'Check-out date is required.',
        'check_out_date.after' => 'Check-out date must be after check-in date.',
        'total_amount.required' => 'Total amount is required.',
    ];

    public function mount($propertyId = null)
    {
        if ($propertyId) {
            $this->property_id = $propertyId;
        }
    }

    public function loadProperties()
    {
        $this->properties = Property::where('owner_id', auth()->id())->get()->toArray();
    }

    public function loadAccommodations()
    {
        if (!$this->property_id) {
            $this->accommodations = [];
            return;
        }
        $this->accommodations = PropertyAccommodation::with('predefinedType')
            ->where('property_id', $this->property_id)
            ->get()
            ->map(function($acc) {
                return [
                    'id' => $acc->id,
                    'display_name' => $acc->display_name,
                    'base_rate' => $acc->base_price,
                ];
            })->toArray();
    }

    public function loadGuests()
    {
        $this->guests = Guest::orderBy('name')->get()->toArray();
    }

    public function loadPartners()
    {
        $this->partners = B2bPartner::where('status', 'active')->get()->toArray();
    }

    public function open($mode = 'quick')
    {
        $this->mode = $mode;
        $this->isOpen = true;
        $this->resetForm();
        $this->loadProperties();
        $this->loadGuests();
        $this->loadPartners();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
        $this->dispatch('modal-closed');
    }

    public function resetForm()
    {
        $this->accommodation_id = null;
        $this->check_in_date = null;
        $this->check_out_date = null;
        $this->adults = 1;
        $this->children = 0;
        $this->guest_id = null;
        $this->b2b_partner_id = null;
        $this->total_amount = 0;
        $this->advance_paid = 0;
        $this->balance_pending = 0;
        $this->rate_override = null;
        $this->override_reason = null;
        $this->special_requests = null;
        $this->notes = null;
        $this->guest_name = null;
        $this->guest_mobile = null;
        $this->guest_email = null;
        $this->partner_mobile = null;
        $this->partner_name = null;
        $this->create_new_guest = false;
        $this->create_new_partner = false;
        $this->base_rate = 0;
        $this->nights = 0;
        $this->applicable_discounts = [];
    }

    public function updatedPropertyId()
    {
        $this->accommodation_id = null;
        $this->loadAccommodations();
        $this->calculateRate();
    }

    public function updatedAccommodationId()
    {
        $this->calculateRate();
    }

    public function updatedCheckInDate()
    {
        $this->calculateNights();
        $this->calculateRate();
    }

    public function updatedCheckOutDate()
    {
        $this->calculateNights();
        $this->calculateRate();
    }

    public function updatedB2bPartnerId()
    {
        $this->calculateRate();
    }

    public function updatedGuestMobile()
    {
        if ($this->guest_mobile) {
            $existingGuest = Guest::findByMobile($this->guest_mobile);
            if ($existingGuest) {
                $this->guest_id = $existingGuest->id;
                $this->guest_name = $existingGuest->name;
                $this->guest_email = $existingGuest->email;
                $this->create_new_guest = false;
            } else {
                $this->guest_id = null;
                $this->create_new_guest = true;
            }
        }
    }

    public function updatedPartnerMobile()
    {
        if ($this->partner_mobile) {
            $existingPartner = B2bPartner::findByUserMobile($this->partner_mobile);
            if ($existingPartner) {
                $this->b2b_partner_id = $existingPartner->id;
                $this->partner_name = $existingPartner->partner_name;
                $this->create_new_partner = false;
            } else {
                $this->b2b_partner_id = null;
                $this->create_new_partner = true;
            }
        }
    }

    public function updatedTotalAmount()
    {
        $this->calculateBalance();
    }

    public function updatedAdvancePaid()
    {
        $this->calculateBalance();
    }



    private function calculateNights()
    {
        if ($this->check_in_date && $this->check_out_date) {
            $checkIn = Carbon::parse($this->check_in_date);
            $checkOut = Carbon::parse($this->check_out_date);
            $this->nights = $checkIn->diffInDays($checkOut);
        }
    }

    private function calculateRate()
    {
        if (!$this->accommodation_id || !$this->check_in_date || !$this->check_out_date) {
            return;
        }

        $bookingService = new BookingService();
        $calculation = $bookingService->calculateRate(
            $this->accommodation_id,
            $this->check_in_date,
            $this->check_out_date,
            $this->b2b_partner_id
        );

        if ($calculation) {
            $this->base_rate = $calculation['base_rate'];
            $this->nights = $calculation['nights'];
            $this->total_amount = $calculation['total_amount'];
            $this->applicable_discounts = $calculation['discounts'];
            $this->calculateBalance();
        }
    }

    private function calculateBalance()
    {
        $this->balance_pending = (float)$this->total_amount - (float)$this->advance_paid;
    }

    public function save()
    {
        try {
            // Simple validation
            if (!$this->property_id) {
                session()->flash('error', 'Please select a property.');
                return;
            }
            
            if (!$this->accommodation_id) {
                session()->flash('error', 'Please select an accommodation.');
                return;
            }
            
            if (!$this->check_in_date || !$this->check_out_date) {
                session()->flash('error', 'Please select check-in and check-out dates.');
                return;
            }
            
            // Simple past date check
            if (!$this->allow_past_dates) {
                if (Carbon::parse($this->check_in_date)->isPast()) {
                    session()->flash('error', 'Check-in date cannot be in the past.');
                    return;
                }
                if (Carbon::parse($this->check_out_date)->isPast()) {
                    session()->flash('error', 'Check-out date cannot be in the past.');
                    return;
                }
            }
            
            if (!$this->guest_id && !$this->create_new_guest) {
                session()->flash('error', 'Please select a guest or create a new one.');
                return;
            }
            
            if ($this->create_new_guest && (!$this->guest_name || !$this->guest_mobile)) {
                session()->flash('error', 'Please enter guest name and mobile number.');
                return;
            }
            
            // Create guest if needed
            if ($this->create_new_guest) {
                $guest = Guest::create([
                    'name' => $this->guest_name,
                    'mobile_number' => $this->guest_mobile,
                    'email' => $this->guest_email,
                ]);
                $this->guest_id = $guest->id;
            }
            
            // Create B2B partner if needed
            if ($this->create_new_partner && $this->partner_mobile) {
                $partner = B2bPartner::createPartnershipRequest(
                    auth()->id(),
                    $this->partner_mobile,
                    $this->partner_name
                );
                $this->b2b_partner_id = $partner->id;
            }
            
            // Create booking
            $booking = Reservation::create([
                'guest_id' => $this->guest_id,
                'property_accommodation_id' => $this->accommodation_id,
                'b2b_partner_id' => $this->b2b_partner_id,
                'check_in_date' => $this->check_in_date,
                'check_out_date' => $this->check_out_date,
                'adults' => $this->adults,
                'children' => $this->children,
                'total_amount' => $this->total_amount ?: 1000,
                'advance_paid' => $this->advance_paid ?: 0,
                'balance_pending' => ($this->total_amount ?: 1000) - ($this->advance_paid ?: 0),
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);
            
            session()->flash('success', 'Booking created successfully!');
            $this->dispatch('booking-created');
            $this->close();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.booking-modal');
    }
}