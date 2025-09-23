<?php

namespace App\Filament\Resources\StarterUserResource\Pages;

use App\Filament\Resources\StarterUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStarterUsers extends ListRecords
{
    protected static string $resource = StarterUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
