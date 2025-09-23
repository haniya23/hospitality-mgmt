<?php

namespace App\Filament\Resources\B2bPartnerResource\Pages;

use App\Filament\Resources\B2bPartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListB2bPartners extends ListRecords
{
    protected static string $resource = B2bPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
