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

    public $view = 'list'; // 'list' or 'calendar'
    public $property_id;
    public $accommodation_id;
    public $rule_type = '';
    public $search = '';
    public $is_active = '';
    
    // Calendar specific
    public $currentMonth;
    public $currentYear;
    public $pricingRules = [];

    protected $listeners = [
        'pricing-rule-saved' => 'refreshData',
        'pricing-rule-deleted' => 'refreshData',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'property_id' => ['except' => ''],
        'rule_type' => ['except' => ''],
        'is_active' => ['except' => ''],
        'view' => ['except' => 'list'],
    ];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->loadPricingRules();
    }

    public function updatedView()
    {
        if ($this->view === 'calendar') {
            $this->loadPricingRules();
        }
        $this->resetPage();
    }

    public function updatedPropertyId()
    {
        $this->accommodation_id = null;
        $this->resetPage();
        if ($this->view === 'calendar') {
            $this->loadPricingRules();
        }
    }

    public function updatedAccommodationId()
    {
        $this->resetPage();
        if ($this->view === 'calendar') {
            $this->loadPricingRules();
        }
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
        if ($this->view === 'calendar') {
            $this->loadPricingRules();
        }
        $this->resetPage();
    }

    // Calendar methods
    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->loadPricingRules();
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->loadPricingRules();
    }

    public function selectDate($date)
    {
        $this->openModal($this->property_id, $date);
    }

    private function loadPricingRules()
    {
        if ($this->view !== 'calendar') return;

        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $query = PricingRule::query();
        
        if ($this->property_id) {
            $query->where('property_id', $this->property_id);
        } else {
            $query->whereIn('property_id', Property::where('owner_id', auth()->id())->pluck('id'));
        }

        if ($this->accommodation_id) {
            $query->where('accommodation_id', $this->accommodation_id);
        }

        $this->pricingRules = $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            })
            ->orderBy('priority', 'desc')
            ->get()
            ->groupBy(function($rule) {
                return $rule->start_date->format('Y-m-d');
            });
    }

    public function getCalendarDays()
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $calendarStart = $startDate->copy()->startOfWeek();
        $calendarEnd = $endDate->copy()->endOfWeek();
        
        $days = [];
        $current = $calendarStart->copy();
        
        while ($current <= $calendarEnd) {
            $dateStr = $current->format('Y-m-d');
            $dayRules = $this->pricingRules[$dateStr] ?? collect();
            
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month == $this->currentMonth,
                'isToday' => $current->isToday(),
                'rules' => $dayRules,
                'ruleCount' => $dayRules->count(),
                'hasPromo' => $dayRules->where('rule_type', 'promotional')->count() > 0,
                'hasSeasonal' => $dayRules->where('rule_type', 'seasonal')->count() > 0,
                'hasB2B' => $dayRules->where('rule_type', 'b2b_contract')->count() > 0,
            ];
            
            $current->addDay();
        }
        
        return collect($days)->chunk(7);
    }

    public function render()
    {
        $properties = Property::where('owner_id', auth()->id())->get();
        $accommodations = $this->property_id 
            ? PropertyAccommodation::where('property_id', $this->property_id)->get() 
            : collect();

        $pricingRules = collect();
        
        if ($this->view === 'list') {
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
        }

        return view('livewire.pricing-management', [
            'properties' => $properties,
            'accommodations' => $accommodations,
            'pricingRules' => $pricingRules,
            'calendarWeeks' => $this->view === 'calendar' ? $this->getCalendarDays() : collect(),
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y'),
        ]);
    }
}