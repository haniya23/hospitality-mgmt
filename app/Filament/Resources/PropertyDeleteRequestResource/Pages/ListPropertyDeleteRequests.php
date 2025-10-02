<?php

namespace App\Filament\Resources\PropertyDeleteRequestResource\Pages;

use App\Filament\Resources\PropertyDeleteRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyDeleteRequests extends ListRecords
{
    protected static string $resource = PropertyDeleteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
