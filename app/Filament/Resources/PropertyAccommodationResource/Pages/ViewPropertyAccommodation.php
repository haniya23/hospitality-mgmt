<?php

namespace App\Filament\Resources\PropertyAccommodationResource\Pages;

use App\Filament\Resources\PropertyAccommodationResource;
use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPropertyAccommodation extends ViewRecord
{
    protected static string $resource = PropertyAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('toggle_active')
                ->label(fn () => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                ->color(fn () => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('create_booking')
                ->label('Create Booking')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url(fn () => \App\Filament\Resources\ReservationResource::getUrl('create', [
                    'property_accommodation_id' => $this->record->id
                ]))
                ->visible(fn () => $this->record->is_active),

            Actions\Action::make('view_bookings')
                ->label('View All Bookings')
                ->icon('heroicon-o-calendar-days')
                ->url(fn () => \App\Filament\Resources\ReservationResource::getUrl('index', [
                    'tableFilters[accommodation][value]' => $this->record->id
                ]))
                ->color('info'),

            Actions\Action::make('view_property')
                ->label('View Property')
                ->icon('heroicon-o-building-office')
                ->url(fn () => \App\Filament\Resources\PropertyResource::getUrl('view', ['record' => $this->record->property_id]))
                ->color('gray'),

            Actions\Action::make('duplicate')
                ->label('Duplicate Accommodation')
                ->icon('heroicon-o-document-duplicate')
                ->color('secondary')
                ->form([
                    \Filament\Forms\Components\TextInput::make('custom_name')
                        ->label('New Accommodation Name')
                        ->required()
                        ->default($this->record->custom_name . ' (Copy)'),
                ])
                ->action(function (array $data) {
                    $newAccommodation = $this->record->replicate();
                    $newAccommodation->custom_name = $data['custom_name'];
                    $newAccommodation->save();

                    // Copy amenities
                    $newAccommodation->amenities()->sync($this->record->amenities->pluck('id'));

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $newAccommodation]));
                }),
        ];
    }
}
