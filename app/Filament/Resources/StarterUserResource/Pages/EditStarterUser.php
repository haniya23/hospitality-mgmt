<?php

namespace App\Filament\Resources\StarterUserResource\Pages;

use App\Filament\Resources\StarterUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStarterUser extends EditRecord
{
    protected static string $resource = StarterUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
