<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->label('Approve Property')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]);
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'pending_approval'),

            Actions\Action::make('reject')
                ->label('Reject Property')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->maxLength(500),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'pending_approval'),

            Actions\Action::make('activate')
                ->label('Activate Property')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'active']);
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'approved'),

            Actions\Action::make('deactivate')
                ->label('Deactivate Property')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'inactive']);
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'active'),

            Actions\Action::make('view_accommodations')
                ->label('View Accommodations')
                ->icon('heroicon-o-home')
                ->url(fn () => \App\Filament\Resources\PropertyAccommodationResource::getUrl('index', ['tableFilters[property][value]' => $this->record->id]))
                ->color('info'),

            Actions\Action::make('view_bookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->url(fn () => \App\Filament\Resources\ReservationResource::getUrl('index', ['tableFilters[property][value]' => $this->record->id]))
                ->color('primary'),
        ];
    }
}
