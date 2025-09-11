<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\PricingRule;
use App\Models\PropertyAccommodation;
use Carbon\Carbon;

class PricingCalendar extends Component
{
    public $property_id;
    public $accommodation_id;
    public $currentMonth;
    public $currentYear;
    public $pricingRules = [];
    public $showRuleModal = false;
    public $selectedDate = null;
    
    // Rule form data
    public $rule_name;
    public $rule_type = 'seasonal';
    public $start_date;
    public $end_date;
    public $rate_adjustment;
    public $percentage_adjustment;
    public $min_stay_nights;
    public $is_active = true;

    protected $rules = [
        'rule_name' => 'required|string|max:255',
        'rule_type' => 'required|in:seasonal,promotional,b2b_contract,loyalty_discount',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'rate_adjustment' => 'nullable|numeric',
        'percentage_adjustment' => 'nullable|numeric|min:-100|max:1000',
    ];

    public function mount($propertyId = null)
    {
        $this->property_id = $propertyId;
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->loadPricingRules();
    }

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
        $this->selectedDate = $date;
        $this->start_date = $date;
        $this->end_date = Carbon::parse($date)->addDays(6)->format('Y-m-d');
        $this->openRuleModal();
    }

    public function openRuleModal()
    {
        $this->showRuleModal = true;
    }

    public function closeRuleModal()
    {
        $this->showRuleModal = false;
        $this->resetRuleForm();
    }

    public function resetRuleForm()
    {
        $this->rule_name = null;
        $this->rule_type = 'seasonal';
        $this->start_date = null;
        $this->end_date = null;
        $this->rate_adjustment = null;
        $this->percentage_adjustment = null;
        $this->min_stay_nights = null;
        $this->is_active = true;
    }

    public function saveRule()
    {
        $this->validate();

        PricingRule::create([
            'property_id' => $this->property_id,
            'rule_name' => $this->rule_name,
            'rule_type' => $this->rule_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rate_adjustment' => $this->rate_adjustment,
            'percentage_adjustment' => $this->percentage_adjustment,
            'min_stay_nights' => $this->min_stay_nights,
            'is_active' => $this->is_active,
            'priority' => $this->calculatePriority(),
        ]);

        $this->loadPricingRules();
        $this->closeRuleModal();
        session()->flash('success', 'Pricing rule created successfully!');
    }

    public function deleteRule($ruleId)
    {
        PricingRule::find($ruleId)?->delete();
        $this->loadPricingRules();
        session()->flash('success', 'Pricing rule deleted.');
    }

    private function loadPricingRules()
    {
        if (!$this->property_id) return;

        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $this->pricingRules = PricingRule::where('property_id', $this->property_id)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->orderBy('priority', 'desc')
            ->get()
            ->groupBy(function($rule) {
                return $rule->start_date->format('Y-m-d');
            });
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
            ];
            
            $current->addDay();
        }
        
        return collect($days)->chunk(7);
    }

    public function render()
    {
        return view('livewire.pricing-calendar', [
            'calendarWeeks' => $this->getCalendarDays(),
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y'),
            'properties' => Property::where('owner_id', auth()->id())->get(),
            'accommodations' => $this->property_id ? PropertyAccommodation::where('property_id', $this->property_id)->get() : collect(),
        ]);
    }
}