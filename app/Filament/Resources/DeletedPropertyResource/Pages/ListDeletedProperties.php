<?php

namespace App\Filament\Resources\DeletedPropertyResource\Pages;

use App\Filament\Resources\DeletedPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeletedProperties extends ListRecords
{
    protected static string $resource = DeletedPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
