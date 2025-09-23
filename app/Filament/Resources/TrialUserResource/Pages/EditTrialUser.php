<?php

namespace App\Filament\Resources\TrialUserResource\Pages;

use App\Filament\Resources\TrialUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrialUser extends EditRecord
{
    protected static string $resource = TrialUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
