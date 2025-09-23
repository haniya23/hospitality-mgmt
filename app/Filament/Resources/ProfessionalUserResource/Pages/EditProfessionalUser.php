<?php

namespace App\Filament\Resources\ProfessionalUserResource\Pages;

use App\Filament\Resources\ProfessionalUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProfessionalUser extends EditRecord
{
    protected static string $resource = ProfessionalUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
