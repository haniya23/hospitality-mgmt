<?php

namespace App\Filament\Resources\StaffAssignmentResource\Pages;

use App\Filament\Resources\StaffAssignmentResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditStaffAssignment extends EditRecord
{
    protected static string $resource = StaffAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add user data to form data for editing
        $user = $this->record->user;
        $data['name'] = $user->name;
        $data['mobile_number'] = $user->mobile_number;
        $data['email'] = $user->email;
        $data['pin'] = ''; // Don't show PIN for security
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update the user data
        $user = $this->record->user;
        $user->update([
            'name' => $data['name'],
            'mobile_number' => $data['mobile_number'],
            'email' => $data['email'] ?? null,
        ]);
        
        // Update PIN if provided
        if (!empty($data['pin'])) {
            $user->update([
                'pin' => Hash::make($data['pin']),
            ]);
        }
        
        // Remove user fields from assignment data
        unset($data['name'], $data['mobile_number'], $data['email'], $data['pin']);
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
