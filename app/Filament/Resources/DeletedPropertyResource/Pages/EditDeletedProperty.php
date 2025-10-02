<?php

namespace App\Filament\Resources\DeletedPropertyResource\Pages;

use App\Filament\Resources\DeletedPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeletedProperty extends EditRecord
{
    protected static string $resource = DeletedPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
