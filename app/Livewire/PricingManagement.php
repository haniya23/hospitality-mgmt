<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Property;
use App\Models\PricingRule;
use App\Models\PropertyAccommodation;
use Carbon\Carbon;

class PricingManagement extends Component
{
    use WithPagination;

    public $property_id;
    public $accommodation_id;
    public $rule_type = '';
    public $search = '';
    public $is_active = '';

    protected $listeners = [
        'pricing-rule-saved' => 'refreshData',
        'pricing-rule-deleted' => 'refreshData',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'property_id' => ['except' => ''],
        'rule_type' => ['except' => ''],
        'is_active' => ['except' => ''],
    ];

    public function mount()
    {
        // Initialize component
    }

    public function updatedPropertyId()
    {
        $this->accommodation_id = null;
        $this->resetPage();
    }

    public function updatedAccommodationId()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRuleType()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['property_id', 'accommodation_id', 'rule_type', 'search', 'is_active']);
        $this->resetPage();
    }

    public function openModal($propertyId = null, $date = null)
    {
        $this->dispatch('open-pricing-modal', $propertyId, $date);
    }

    public function editRule($ruleId)
    {
        $this->dispatch('edit-pricing-rule', $ruleId);
    }

    public function deleteRule($ruleId)
    {
        PricingRule::find($ruleId)?->delete();
        session()->flash('success', 'Pricing rule deleted successfully!');
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $properties = Property::where('owner_id', auth()->id())->get();
        $accommodations = $this->property_id 
            ? PropertyAccommodation::where('property_id', $this->property_id)->get() 
            : collect();

        $query = PricingRule::with(['property', 'accommodation', 'b2bPartner'])
            ->whereIn('property_id', $properties->pluck('id'));

        if ($this->property_id) {
            $query->where('property_id', $this->property_id);
        }

        if ($this->accommodation_id) {
            $query->where('accommodation_id', $this->accommodation_id);
        }

        if ($this->rule_type) {
            $query->where('rule_type', $this->rule_type);
        }

        if ($this->is_active !== '') {
            $query->where('is_active', $this->is_active);
        }

        if ($this->search) {
            $query->where('rule_name', 'like', '%' . $this->search . '%');
        }

        $pricingRules = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.pricing-management', [
            'properties' => $properties,
            'accommodations' => $accommodations,
            'pricingRules' => $pricingRules,
        ]);
    }
}