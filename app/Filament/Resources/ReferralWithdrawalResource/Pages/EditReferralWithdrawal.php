<?php

namespace App\Filament\Resources\ReferralWithdrawalResource\Pages;

use App\Filament\Resources\ReferralWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferralWithdrawal extends EditRecord
{
    protected static string $resource = ReferralWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
