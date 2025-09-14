<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\PricingRule;
use Carbon\Carbon;

class PricingModal extends Component
{
    public $isOpen = false;
    public $mode = 'create'; // 'create' or 'edit'
    public $ruleId = null;
    
    protected $listeners = [
        'open-pricing-modal' => 'open',
        'edit-pricing-rule' => 'edit',
    ];
    
    // Form data
    public $property_id;
    public $accommodation_id;
    public $rule_name;
    public $rule_type = 'seasonal';
    public $start_date;
    public $end_date;
    public $rate_adjustment;
    public $percentage_adjustment;
    public $min_stay_nights;
    public $max_stay_nights;
    public $applicable_days = [];
    public $b2b_partner_id;
    public $promo_code;
    public $is_active = true;
    
    // Cached collections
    public $properties = [];
    public $accommodations = [];
    public $b2bPartners = [];

    protected function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:seasonal,promotional,b2b_contract,loyalty_discount',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rate_adjustment' => 'nullable|numeric',
            'percentage_adjustment' => 'nullable|numeric|min:-100|max:1000',
            'min_stay_nights' => 'nullable|integer|min:1',
            'max_stay_nights' => 'nullable|integer|min:1',
        ];
    }

    protected $messages = [
        'property_id.required' => 'Please select a property.',
        'rule_name.required' => 'Rule name is required.',
        'start_date.required' => 'Start date is required.',
        'end_date.required' => 'End date is required.',
        'end_date.after' => 'End date must be after start date.',
    ];

    public function mount()
    {
        $this->loadProperties();
        $this->loadB2bPartners();
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
                    'base_price' => $acc->base_price,
                ];
            })->toArray();
    }

    public function loadB2bPartners()
    {
        $this->b2bPartners = \App\Models\B2bPartner::where('status', 'active')->get()->toArray();
    }

    public function open($propertyId = null, $date = null)
    {
        $this->mode = 'create';
        $this->isOpen = true;
        $this->resetForm();
        
        if ($propertyId) {
            $this->property_id = $propertyId;
            $this->loadAccommodations();
        }
        
        if ($date) {
            $this->start_date = $date;
            $this->end_date = Carbon::parse($date)->addDays(6)->format('Y-m-d');
        }
    }

    public function edit($ruleId)
    {
        $rule = PricingRule::find($ruleId);
        if (!$rule) return;

        $this->mode = 'edit';
        $this->ruleId = $ruleId;
        $this->isOpen = true;
        
        $this->property_id = $rule->property_id;
        $this->accommodation_id = $rule->accommodation_id;
        $this->rule_name = $rule->rule_name;
        $this->rule_type = $rule->rule_type;
        $this->start_date = $rule->start_date->format('Y-m-d');
        $this->end_date = $rule->end_date->format('Y-m-d');
        $this->rate_adjustment = $rule->rate_adjustment;
        $this->percentage_adjustment = $rule->percentage_adjustment;
        $this->min_stay_nights = $rule->min_stay_nights;
        $this->max_stay_nights = $rule->max_stay_nights;
        $this->applicable_days = $rule->applicable_days ?? [];
        $this->b2b_partner_id = $rule->b2b_partner_id;
        $this->promo_code = $rule->promo_code;
        $this->is_active = $rule->is_active;
        
        $this->loadAccommodations();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
        $this->dispatch('modal-closed');
    }

    public function resetForm()
    {
        $this->ruleId = null;
        $this->property_id = null;
        $this->accommodation_id = null;
        $this->rule_name = null;
        $this->rule_type = 'seasonal';
        $this->start_date = null;
        $this->end_date = null;
        $this->rate_adjustment = null;
        $this->percentage_adjustment = null;
        $this->min_stay_nights = null;
        $this->max_stay_nights = null;
        $this->applicable_days = [];
        $this->b2b_partner_id = null;
        $this->promo_code = null;
        $this->is_active = true;
    }

    public function updatedPropertyId()
    {
        $this->accommodation_id = null;
        $this->loadAccommodations();
    }

    public function save()
    {
        try {
            $this->validate();

            $data = [
                'property_id' => $this->property_id,
                'accommodation_id' => $this->accommodation_id,
                'rule_name' => $this->rule_name,
                'rule_type' => $this->rule_type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'rate_adjustment' => $this->rate_adjustment,
                'percentage_adjustment' => $this->percentage_adjustment,
                'min_stay_nights' => $this->min_stay_nights,
                'max_stay_nights' => $this->max_stay_nights,
                'applicable_days' => $this->applicable_days,
                'b2b_partner_id' => $this->b2b_partner_id,
                'promo_code' => $this->promo_code,
                'is_active' => $this->is_active,
                'priority' => $this->calculatePriority(),
            ];

            if ($this->mode === 'edit' && $this->ruleId) {
                PricingRule::find($this->ruleId)->update($data);
                session()->flash('success', 'Pricing rule updated successfully!');
            } else {
                PricingRule::create($data);
                session()->flash('success', 'Pricing rule created successfully!');
            }

            $this->dispatch('pricing-rule-saved');
            $this->close();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        if ($this->ruleId) {
            PricingRule::find($this->ruleId)?->delete();
            session()->flash('success', 'Pricing rule deleted successfully!');
            $this->dispatch('pricing-rule-deleted');
            $this->close();
        }
    }

    private function calculatePriority()
    {
        $priorities = [
            'b2b_contract' => 100,
            'promotional' => 80,
            'seasonal' => 60,
            'loyalty_discount' => 40,
        ];
        
        return $priorities[$this->rule_type] ?? 50;
    }

    public function render()
    {
        return view('livewire.pricing-modal');
    }
}