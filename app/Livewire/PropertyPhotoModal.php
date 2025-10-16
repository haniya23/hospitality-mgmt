<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyPhoto;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class PropertyPhotoModal extends Component
{
    public $property;
    public $isOpen = false;
    public $existingMainPhoto;
    public $existingAdditionalPhotos;

    protected $listeners = ['openPhotoModal' => 'open', 'refreshComponent' => 'loadExistingPhotos'];

    public function mount(Property $property)
    {
        $this->property = $property;
        $this->loadExistingPhotos();
    }

    public function loadExistingPhotos()
    {
        $this->existingMainPhoto = $this->property->photos()->where('is_main', true)->first();
        $this->existingAdditionalPhotos = $this->property->photos()
            ->where('is_main', false)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();
    }

    public function removePhoto($id)
    {
        $photo = PropertyPhoto::find($id);
        if ($photo && $photo->property_id === $this->property->id) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }
        $this->loadExistingPhotos();
    }

    public function open()
    {
        $this->isOpen = true;
        $this->loadExistingPhotos();
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function removeAllPhotos()
    {
        foreach ($this->property->photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }
        $this->loadExistingPhotos();
    }

    public function render()
    {
        return view('livewire.property-photo-modal');
    }
}
