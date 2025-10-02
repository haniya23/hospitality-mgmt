<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('check_in')
                ->label('Check In')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $this->record->checkIn())
                ->visible(fn () => $this->record->status === 'confirmed'),
            
            Actions\Action::make('check_out')
                ->label('Check Out')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->color('warning')
                ->requiresConfirmation()
                ->action(fn () => $this->record->checkOut())
                ->visible(fn () => $this->record->status === 'checked_in'),

            Actions\Action::make('mark_completed')
                ->label('Mark Completed')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $this->record->complete())
                ->visible(fn () => $this->record->status === 'checked_out'),

            Actions\Action::make('print_confirmation')
                ->label('Print Confirmation')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => '#') // TODO: Implement print route
                ->openUrlInNewTab(),
        ];
    }
}
