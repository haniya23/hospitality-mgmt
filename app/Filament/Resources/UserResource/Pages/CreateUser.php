<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash the PIN if provided
        if (isset($data['pin']) && !empty($data['pin'])) {
            $data['pin_hash'] = Hash::make($data['pin']);
        }
        
        // Remove the plain PIN from data
        unset($data['pin'], $data['pin_confirmation']);

        // Set default values
        $data['uuid'] = \Illuminate\Support\Str::uuid();
        
        // Set trial details if trial is selected
        if ($data['subscription_status'] === 'trial') {
            $data['is_trial_active'] = true;
            $data['trial_plan'] = $data['trial_plan'] ?? 'professional';
            $data['trial_ends_at'] = $data['trial_ends_at'] ?? now()->addDays(30);
        }

        // Set admin defaults
        if ($data['is_admin']) {
            $data['subscription_status'] = 'admin';
            $data['properties_limit'] = 999;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Log user creation
        activity()
            ->performedOn($this->record)
            ->causedBy(auth()->user())
            ->log('User created');
    }
}
