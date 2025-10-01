<?php

namespace App\Filament\Resources\SubscriptionHistoryResource\Pages;

use App\Filament\Resources\SubscriptionHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionHistories extends ListRecords
{
    protected static string $resource = SubscriptionHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
