<?php

namespace App\Filament\Resources\StaffAssignmentResource\Pages;

use App\Filament\Resources\StaffAssignmentResource;
use App\Models\User;
use App\Models\StaffAssignment;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateStaffAssignment extends CreateRecord
{
    protected static string $resource = StaffAssignmentResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Create the staff user first
        $staffUser = User::create([
            'name' => $data['name'],
            'mobile_number' => $data['mobile_number'],
            'email' => $data['email'] ?? null,
            'pin' => Hash::make($data['pin']),
            'is_staff' => true,
            'is_active' => true,
            'user_type' => 'staff',
        ]);
        
        // Remove user creation fields and add user_id
        unset($data['name'], $data['mobile_number'], $data['email'], $data['pin']);
        $data['user_id'] = $staffUser->id;
        $data['uuid'] = Str::uuid();
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
