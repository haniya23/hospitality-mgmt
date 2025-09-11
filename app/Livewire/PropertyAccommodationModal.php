<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PredefinedAccommodationType;
use App\Models\PropertyAccommodation;
use Livewire\Component;
use Livewire\Attributes\On;

class PropertyAccommodationModal extends Component
{
    public $showModal = false;
    public $property;
    public $accommodationId = null;
    public $predefinedTypeId = null;
    public $customName = '';
    public $maxOccupancy = 2;
    public $basePrice = 0;
    public $description = '';
    public $features = [];
    public $isActive = true;

    protected function rules()
    {
        $rules = [
            'predefinedTypeId' => 'required|exists:predefined_accommodation_types,id',
            'maxOccupancy' => 'required|integer|min:1',
            'basePrice' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'array',
        ];

        // Check if selected type is Custom
        $selectedType = \App\Models\PredefinedAccommodationType::find($this->predefinedTypeId);
        if ($selectedType && $selectedType->name === 'Custom') {
            $rules['customName'] = 'required|string|max:255';
        } else {
            $rules['customName'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    #[On('open-accommodation-modal')]
    public function openModal($propertyId, $accommodationId = null, $predefinedTypeId = null)
    {
        $this->property = Property::findOrFail($propertyId);
        $this->accommodationId = $accommodationId;
        $this->predefinedTypeId = $predefinedTypeId;

        if ($accommodationId) {
            $accommodation = PropertyAccommodation::findOrFail($accommodationId);
            $this->predefinedTypeId = $accommodation->predefined_accommodation_type_id;
            $this->customName = $accommodation->custom_name;
            $this->maxOccupancy = $accommodation->max_occupancy;
            $this->basePrice = $accommodation->base_price;
            $this->description = $accommodation->description;
            $this->features = $accommodation->features ?? [];
            $this->isActive = $accommodation->is_active;
        } else {
            $this->reset(['customName', 'description', 'features']);
            $this->maxOccupancy = 2;
            $this->basePrice = 0;
            $this->isActive = true;
            
            // Set default to Custom accommodation type
            $customType = PredefinedAccommodationType::where('property_category_id', $this->property->property_category_id)
                ->where('name', 'Custom')
                ->first();
            $this->predefinedTypeId = $customType ? $customType->id : null;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetValidation();
        $this->dispatch('close-accommodation-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'property_id' => $this->property->id,
            'predefined_accommodation_type_id' => $this->predefinedTypeId,
            'custom_name' => $this->customName,
            'max_occupancy' => $this->maxOccupancy,
            'base_price' => $this->basePrice,
            'description' => $this->description,
            'features' => $this->features,
            'is_active' => $this->isActive,
        ];

        if ($this->accommodationId) {
            PropertyAccommodation::find($this->accommodationId)->update($data);
        } else {
            PropertyAccommodation::create($data);
        }

        $this->closeModal();
        $this->dispatch('accommodation-updated');
    }

    public function render()
    {
        $predefinedTypes = $this->property 
            ? PredefinedAccommodationType::where('property_category_id', $this->property->property_category_id)
                ->orderBy('name')
                ->get()
            : collect();

        return view('livewire.property-accommodation-modal', [
            'predefinedTypes' => $predefinedTypes,
        ]);
    }
}