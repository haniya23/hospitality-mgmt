<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\Reservation;
use App\Models\PricingRule;
use App\Models\Commission;
use App\Models\AuditLog;
use Carbon\Carbon;

class BookingModal extends Component
{
    public $isOpen = false;
    public $mode = 'quick'; // 'quick' or 'full'
    
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
    
    public $properties;
    public $accommodations = [];
    public $guests = [];
    public $partners = [];

    protected $rules = [
        'property_id' => 'required|exists:properties,id',
        'accommodation_id' => 'required|exists:property_accommodations,id',
        'check_in_date' => 'required|date|after_or_equal:today',
        'check_out_date' => 'required|date|after:check_in_date',
        'adults' => 'required|integer|min:1',
        'children' => 'required|integer|min:0',
        'guest_id' => 'required_without:create_new_guest|exists:guests,id',
        'guest_name' => 'required_if:create_new_guest,true|string|max:255',
        'guest_mobile' => 'required_if:create_new_guest,true|string|max:20',
        'total_amount' => 'required|numeric|min:0',
        'advance_paid' => 'required|numeric|min:0|lte:total_amount',
    ];

    public function mount($propertyId = null)
    {
        $this->properties = Property::where('owner_id', auth()->id())->get();
        $this->guests = Guest::orderBy('name')->get();
        $this->partners = B2bPartner::where('status', 'active')->get();
        
        if ($propertyId) {
            $this->property_id = $propertyId;
            $this->loadAccommodations();
        }
    }

    public function open($mode = 'quick')
    {
        $this->mode = $mode;
        $this->isOpen = true;
        $this->resetForm();
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
        $this->loadAccommodations();
        $this->accommodation_id = null;
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

    private function loadAccommodations()
    {
        if ($this->property_id) {
            $this->accommodations = PropertyAccommodation::where('property_id', $this->property_id)->get();
        }
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

        $accommodation = PropertyAccommodation::find($this->accommodation_id);
        if (!$accommodation) {
            return;
        }

        $this->base_rate = $accommodation->base_rate ?? 0;
        
        // Apply pricing rules
        $pricingRules = PricingRule::getApplicableRules(
            $this->property_id,
            $this->check_in_date,
            $this->check_out_date,
            $this->b2b_partner_id
        );

        $adjustedRate = $this->base_rate;
        $this->applicable_discounts = [];

        foreach ($pricingRules as $rule) {
            $newRate = $rule->calculateAdjustedRate($adjustedRate);
            $this->applicable_discounts[] = [
                'name' => $rule->rule_name,
                'type' => $rule->rule_type,
                'adjustment' => $newRate - $adjustedRate,
            ];
            $adjustedRate = $newRate;
        }

        $this->total_amount = $adjustedRate * $this->nights;
        $this->calculateBalance();
    }

    private function calculateBalance()
    {
        $this->balance_pending = $this->total_amount - $this->advance_paid;
    }

    public function save()
    {
        $this->validate();

        try {
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
                'total_amount' => $this->rate_override ?? $this->total_amount,
                'advance_paid' => $this->advance_paid,
                'balance_pending' => $this->balance_pending,
                'rate_override' => $this->rate_override,
                'override_reason' => $this->override_reason,
                'special_requests' => $this->special_requests,
                'notes' => $this->notes,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Log price override if applicable
            if ($this->rate_override && $this->rate_override != $this->total_amount) {
                AuditLog::logPriceOverride($booking, $this->total_amount, $this->rate_override, $this->override_reason);
            }

            // Create commission record for B2B bookings
            if ($booking->b2b_partner_id) {
                Commission::calculateForBooking($booking);
            }

            $this->dispatch('booking-created', ['booking' => $booking->id]);
            session()->flash('success', 'Booking created successfully!');
            $this->close();

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.booking-modal');
    }
}