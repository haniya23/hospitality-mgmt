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
            Actions\DeleteAction::make(),
        ];
    }
}
