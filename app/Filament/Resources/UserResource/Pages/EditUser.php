<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete this user? This action cannot be undone and will affect all associated data.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove sensitive data from form
        unset($data['pin_hash']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash the PIN if provided and changed
        if (isset($data['pin']) && !empty($data['pin'])) {
            $data['pin_hash'] = Hash::make($data['pin']);
        }
        
        // Remove the plain PIN from data
        unset($data['pin'], $data['pin_confirmation']);

        // Log the changes
        $changes = [];
        $original = $this->record->getOriginal();
        
        $fieldsToTrack = ['name', 'email', 'subscription_status', 'is_admin', 'is_active', 'properties_limit'];
        foreach ($fieldsToTrack as $field) {
            if (isset($data[$field]) && $data[$field] != $original[$field]) {
                $changes[$field] = [
                    'from' => $original[$field],
                    'to' => $data[$field],
                ];
            }
        }

        if (!empty($changes)) {
            activity()
                ->performedOn($this->record)
                ->causedBy(auth()->user())
                ->withProperties(['changes' => $changes])
                ->log('User updated');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Additional logic after save if needed
    }
}
