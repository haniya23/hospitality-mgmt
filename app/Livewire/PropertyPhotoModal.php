<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyPhoto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PropertyPhotoModal extends Component
{
    use WithFileUploads;

    public $property;
    public $isOpen = false;
    public $mainPhoto;
    public $additionalPhotos = [];
    public $existingMainPhoto;
    public $existingAdditionalPhotos;

    protected $listeners = ['openPhotoModal' => 'open'];

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

    public function uploadMainPhoto()
    {
        $this->validate([
            'mainPhoto' => 'required|image|max:2048',
        ]);

        if ($this->existingMainPhoto) {
            $this->removePhoto($this->existingMainPhoto->id);
        }

        $this->processAndSavePhoto($this->mainPhoto, true);
        $this->mainPhoto = null;
        $this->loadExistingPhotos();
    }

    public function uploadAdditionalPhotos()
    {
        $this->validate([
            'additionalPhotos.*' => 'required|image|max:2048',
        ]);

        foreach ($this->additionalPhotos as $photo) {
            if ($this->existingAdditionalPhotos->count() >= 3) break;
            $this->processAndSavePhoto($photo, false);
        }

        $this->additionalPhotos = [];
        $this->loadExistingPhotos();
    }

    private function processAndSavePhoto($photo, $isMain)
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
        Storage::disk('public')->put($path, $imageData);
        
        $this->property->photos()->create([
            'file_path' => $path,
            'is_main' => $isMain,
            'caption' => $isMain ? 'Main Photo' : 'Additional Photo',
            'file_size' => strlen($imageData),
            'sort_order' => $isMain ? 0 : $this->existingAdditionalPhotos->count() + 1,
        ]);
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
        $this->mainPhoto = null;
        $this->additionalPhotos = [];
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

    public function updatedMainPhoto()
    {
        if ($this->mainPhoto) {
            $this->uploadMainPhoto();
        }
    }

    public function updatedAdditionalPhotos()
    {
        if ($this->additionalPhotos) {
            $this->uploadAdditionalPhotos();
        }
    }

    public function render()
    {
        return view('livewire.property-photo-modal');
    }
}
