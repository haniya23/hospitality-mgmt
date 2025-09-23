<?php

namespace App\Filament\Resources\B2bPartnerResource\Pages;

use App\Filament\Resources\B2bPartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditB2bPartner extends EditRecord
{
    protected static string $resource = B2bPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
