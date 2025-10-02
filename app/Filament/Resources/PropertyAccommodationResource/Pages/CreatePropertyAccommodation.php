<?php

namespace App\Filament\Resources\PropertyAccommodationResource\Pages;

use App\Filament\Resources\PropertyAccommodationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyAccommodation extends CreateRecord
{
    protected static string $resource = PropertyAccommodationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function afterCreate(): void
    {
        // Handle amenities
        if (isset($this->data['amenities'])) {
            $this->record->amenities()->sync($this->data['amenities']);
        }

        // Handle photos
        if (isset($this->data['photos']) && is_array($this->data['photos'])) {
            foreach ($this->data['photos'] as $index => $photo) {
                $this->record->photos()->create([
                    'file_path' => $photo,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                    'accommodation_id' => $this->record->id,
                ]);
            }
        }

        // Create reserved customer for this accommodation
        $this->record->getOrCreateReservedCustomer();
    }
}
