<?php

namespace App\Filament\Resources\SubscriptionAddonResource\Pages;

use App\Filament\Resources\SubscriptionAddonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionAddons extends ListRecords
{
    protected static string $resource = SubscriptionAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
