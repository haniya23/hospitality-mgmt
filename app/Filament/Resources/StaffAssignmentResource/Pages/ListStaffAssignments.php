<?php

namespace App\Filament\Resources\StaffAssignmentResource\Pages;

use App\Filament\Resources\StaffAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffAssignments extends ListRecords
{
    protected static string $resource = StaffAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
