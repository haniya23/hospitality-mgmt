<?php

namespace App\Filament\Resources\SubscriptionAddonResource\Pages;

use App\Filament\Resources\SubscriptionAddonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionAddon extends EditRecord
{
    protected static string $resource = SubscriptionAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
