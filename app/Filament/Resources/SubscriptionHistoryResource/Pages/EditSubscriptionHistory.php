<?php

namespace App\Filament\Resources\SubscriptionHistoryResource\Pages;

use App\Filament\Resources\SubscriptionHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionHistory extends EditRecord
{
    protected static string $resource = SubscriptionHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
