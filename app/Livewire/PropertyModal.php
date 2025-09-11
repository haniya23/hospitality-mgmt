<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use App\Models\Amenity;
use App\Models\PropertyPhoto;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PropertyModal extends Component
{
    use WithFileUploads;
    public $showModal = false;
    public $propertyId = null;
    public $section = 'basic';
    
    // Basic Info
    public $name = '';
    public $description = '';
    public $property_category_id = null;
    public $owner_name = '';
    
    // Location
    public $address = '';
    public $country_id = null;
    public $state_id = null;
    public $district_id = null;
    public $city_id = null;
    public $pincode_id = null;
    public $latitude = null;
    public $longitude = null;
    
    // Accommodation
    public $accommodation_name = '';
    public $max_occupancy = null;
    public $base_price = null;
    public $accommodation_description = '';
    
    // Amenities
    public $selectedAmenities = [];
    
    // Policies
    public $check_in_time = '';
    public $check_out_time = '';
    public $cancellation_policy = '';
    public $house_rules = '';
    
    // Photos
    public $photo_caption = 'general';
    public $photos = [];
    public $existingPhotos = [];

    protected function rules()
    {
        return match($this->section) {
            'basic' => [
                'name' => 'required|string|max:255',
                'property_category_id' => 'required|exists:property_categories,id',
                'description' => 'nullable|string',

            ],
            'location' => [
                'address' => 'required|string',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'nullable|exists:states,id',
                'district_id' => 'nullable|exists:districts,id',
                'city_id' => 'nullable|exists:cities,id',
                'pincode_id' => 'nullable|exists:pincodes,id',
            ],
            'accommodation' => [
                'accommodation_name' => 'required|string|max:255',
                'max_occupancy' => 'required|integer|min:1',
                'base_price' => 'required|numeric|min:0',
                'accommodation_description' => 'nullable|string',
            ],
            'amenities' => [
                'selectedAmenities' => 'array',
            ],
            'policies' => [
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
                'cancellation_policy' => 'nullable|string',
                'house_rules' => 'nullable|string',
            ],
            'photos' => [
                'photo_caption' => 'required|string|max:255',
                'photos.*' => 'image|max:2048',
            ],
            default => []
        };
    }

    #[On('open-property-modal')]
    public function openModal($propertyId, $section = 'basic')
    {
        $property = Property::findOrFail($propertyId);
        $this->propertyId = $propertyId;
        $this->section = $section;
        
        // Load data based on section
        if ($section === 'basic') {
            $this->name = $property->name;
            $this->description = $property->description ?? '';
            $this->property_category_id = $property->property_category_id;
            $this->owner_name = $property->owner->name;
        } elseif ($section === 'location') {
            $location = $property->location;
            $this->address = $location->address ?? '';
            $this->country_id = $location->country_id ?? null;
            $this->state_id = $location->state_id ?? null;
            $this->district_id = $location->district_id ?? null;
            $this->city_id = $location->city_id ?? null;
            $this->pincode_id = $location->pincode_id ?? null;
            $this->latitude = $location->latitude ?? null;
            $this->longitude = $location->longitude ?? null;
        } elseif ($section === 'accommodation') {
            $acc = $property->accommodations->first();
            $this->accommodation_name = $acc->name ?? '';
            $this->max_occupancy = $acc->max_occupancy ?? null;
            $this->base_price = $acc->base_price ?? null;
            $this->accommodation_description = $acc->description ?? '';
        } elseif ($section === 'amenities') {
            $this->selectedAmenities = $property->amenities?->pluck('id')->toArray() ?? [];
        } elseif ($section === 'policies') {
            $policy = $property->policy;
            $this->check_in_time = $policy->check_in_time ?? '';
            $this->check_out_time = $policy->check_out_time ?? '';
            $this->cancellation_policy = $policy->cancellation_policy ?? '';
            $this->house_rules = $policy->house_rules ?? '';
        } elseif ($section === 'photos') {
            $this->existingPhotos = $property->photos;
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetValidation();
    }

    public function updatedCountryId()
    {
        $this->state_id = null;
        $this->district_id = null;
        $this->city_id = null;
        $this->pincode_id = null;
    }

    public function updatedStateId()
    {
        $this->district_id = null;
        $this->city_id = null;
        $this->pincode_id = null;
    }

    public function updatedDistrictId()
    {
        $this->city_id = null;
        $this->pincode_id = null;
    }

    public function updatedCityId()
    {
        $this->pincode_id = null;
    }

    public function save()
    {
        $this->validate();
        $property = Property::find($this->propertyId);
        
        if ($this->section === 'basic') {
            $property->update([
                'name' => $this->name,
                'description' => $this->description,
                'property_category_id' => $this->property_category_id,
            ]);
        } elseif ($this->section === 'location') {
            $property->location()->updateOrCreate(
                [],
                [
                    'property_id' => $property->id,
                    'address' => $this->address,
                    'country_id' => $this->country_id,
                    'state_id' => $this->state_id,
                    'district_id' => $this->district_id,
                    'city_id' => $this->city_id,
                    'pincode_id' => $this->pincode_id,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                ]
            );
        } elseif ($this->section === 'accommodation') {
            $property->accommodations()->updateOrCreate([], [
                'name' => $this->accommodation_name,
                'max_occupancy' => $this->max_occupancy,
                'base_price' => $this->base_price,
                'description' => $this->accommodation_description,
            ]);
        } elseif ($this->section === 'amenities') {
            $property->amenities()->sync($this->selectedAmenities);
        } elseif ($this->section === 'policies') {
            $property->policy()->updateOrCreate([], [
                'check_in_time' => $this->check_in_time,
                'check_out_time' => $this->check_out_time,
                'cancellation_policy' => $this->cancellation_policy,
                'house_rules' => $this->house_rules,
            ]);
        } elseif ($this->section === 'photos') {
            $this->uploadPhotos($property);
        }

        $this->closeModal();
        $this->dispatch('property-updated');
    }

    public function uploadPhotos($property)
    {
        if ($property->photos()->count() >= 3) {
            $this->addError('photos', 'Maximum 3 photos allowed per property.');
            return;
        }

        foreach ($this->photos as $photo) {
            if ($property->photos()->count() >= 3) break;
            $this->processAndSavePhoto($photo, $property);
        }
        $this->photos = [];
    }

    private function processAndSavePhoto($photo, $property)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo->getRealPath());
        $image->scaleDown(1200, 800);
        $imageData = $image->toJpeg(85);
        
        if (strlen($imageData) > 512 * 1024) {
            $image->scaleDown(800, 600);
            $imageData = $image->toJpeg(75);
        }
        
        $filename = uniqid() . '.jpg';
        $path = 'property-photos/' . $filename;
        \Storage::disk('public')->put($path, $imageData);
        
        PropertyPhoto::create([
            'property_id' => $property->id,
            'file_path' => $path,
            'caption' => $this->photo_caption,
        ]);
    }

    public function deletePhoto($photoId)
    {
        $photo = PropertyPhoto::find($photoId);
        if ($photo) {
            \Storage::disk('public')->delete($photo->file_path);
            $photo->delete();
            $this->existingPhotos = $this->existingPhotos->reject(fn($p) => $p->id === $photoId);
        }
    }



    public function render()
    {
        return view('livewire.property-modal', [
            'categories' => PropertyCategory::all(),
            'countries' => Country::all(),
            'states' => $this->country_id ? State::where('country_id', $this->country_id)->get() : collect(),
            'districts' => $this->state_id ? District::where('state_id', $this->state_id)->get() : collect(),
            'cities' => $this->district_id ? City::where('district_id', $this->district_id)->get() : collect(),
            'pincodes' => $this->city_id ? Pincode::where('city_id', $this->city_id)->get() : collect(),
            'amenities' => Amenity::all(),
            'photoCaptions' => ['general', 'balcony', 'bedroom', 'master_room', 'hall', 'dining', 'tea_spot', 'kitchen', 'bathroom', 'exterior']
        ]);
    }
}