<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Reservations'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count()),
            'confirmed' => Tab::make('Confirmed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
                ->badge(fn () => $this->getModel()::where('status', 'confirmed')->count()),
            'checked_in' => Tab::make('Checked In')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'checked_in'))
                ->badge(fn () => $this->getModel()::where('status', 'checked_in')->count()),
            'today_arrivals' => Tab::make('Today Arrivals')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('check_in_date', today()))
                ->badge(fn () => $this->getModel()::whereDate('check_in_date', today())->count()),
            'today_departures' => Tab::make('Today Departures')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('check_out_date', today()))
                ->badge(fn () => $this->getModel()::whereDate('check_out_date', today())->count()),
        ];
    }
}
