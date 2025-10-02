<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->canBeDeleted()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load location data
        if ($this->record->location) {
            $data['location'] = $this->record->location->toArray();
        }

        // Load policy data
        if ($this->record->policy) {
            $data['policy'] = $this->record->policy->toArray();
        }

        // Load amenities
        $data['amenities'] = $this->record->amenities->pluck('id')->toArray();

        // Load photos
        $data['photos'] = $this->record->photos->pluck('file_path')->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update wizard step if moving forward
        if (isset($data['wizard_step_completed'])) {
            $data['wizard_step_completed'] = max($this->record->wizard_step_completed, $data['wizard_step_completed']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Update location
        if (isset($this->data['location']) && is_array($this->data['location'])) {
            $locationData = $this->data['location'];
            $locationData['property_id'] = $this->record->id;
            
            $this->record->location()->updateOrCreate(
                ['property_id' => $this->record->id],
                $locationData
            );
        }

        // Update policy
        if (isset($this->data['policy']) && is_array($this->data['policy'])) {
            $policyData = $this->data['policy'];
            $policyData['property_id'] = $this->record->id;
            
            $this->record->policy()->updateOrCreate(
                ['property_id' => $this->record->id],
                $policyData
            );
        }

        // Update amenities
        if (isset($this->data['amenities'])) {
            $this->record->amenities()->sync($this->data['amenities']);
        }

        // Update photos
        if (isset($this->data['photos']) && is_array($this->data['photos'])) {
            // Remove existing photos
            $this->record->photos()->delete();
            
            // Add new photos
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
