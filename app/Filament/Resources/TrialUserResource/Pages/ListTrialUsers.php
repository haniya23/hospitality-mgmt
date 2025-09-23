<?php

namespace App\Filament\Resources\TrialUserResource\Pages;

use App\Filament\Resources\TrialUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrialUsers extends ListRecords
{
    protected static string $resource = TrialUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
