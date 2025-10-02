<?php

namespace App\Filament\Resources\PropertyDeleteRequestResource\Pages;

use App\Filament\Resources\PropertyDeleteRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyDeleteRequest extends EditRecord
{
    protected static string $resource = PropertyDeleteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
