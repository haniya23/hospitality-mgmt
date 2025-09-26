<?php

namespace App\Filament\Resources\PropertyAccommodationResource\Pages;

use App\Filament\Resources\PropertyAccommodationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyAccommodations extends ListRecords
{
    protected static string $resource = PropertyAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
