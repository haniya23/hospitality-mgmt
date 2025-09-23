<?php

namespace App\Filament\Resources\ProfessionalUserResource\Pages;

use App\Filament\Resources\ProfessionalUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfessionalUsers extends ListRecords
{
    protected static string $resource = ProfessionalUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
