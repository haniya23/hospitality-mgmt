<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = $data['owner_id'] ?? auth()->id();
        $data['wizard_step_completed'] = 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create location record if location data exists
        if (isset($this->data['location']) && is_array($this->data['location'])) {
            $locationData = $this->data['location'];
            $locationData['property_id'] = $this->record->id;
            
            $this->record->location()->create($locationData);
        }

        // Create policy record if policy data exists
        if (isset($this->data['policy']) && is_array($this->data['policy'])) {
            $policyData = $this->data['policy'];
            $policyData['property_id'] = $this->record->id;
            
            $this->record->policy()->create($policyData);
        }

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
                ]);
            }
        }
    }
}
