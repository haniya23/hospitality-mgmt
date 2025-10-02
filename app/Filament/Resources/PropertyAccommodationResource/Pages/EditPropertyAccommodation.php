<?php

namespace App\Filament\Resources\PropertyAccommodationResource\Pages;

use App\Filament\Resources\PropertyAccommodationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyAccommodation extends EditRecord
{
    protected static string $resource = PropertyAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete this accommodation? This action cannot be undone and will affect any existing bookings.')
                ->visible(fn () => $this->record->reservations()->count() === 0),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load amenities
        $data['amenities'] = $this->record->amenities->pluck('id')->toArray();

        // Load photos
        $data['photos'] = $this->record->photos->pluck('file_path')->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
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
                    'accommodation_id' => $this->record->id,
                ]);
            }
        }

        // Update reserved customer name if accommodation name changed
        if ($this->record->wasChanged('custom_name')) {
            $this->record->updateReservedCustomerName();
        }
    }
}
