<?php

namespace App\Filament\Resources\PropertyAccommodationResource\Pages;

use App\Filament\Resources\PropertyAccommodationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPropertyAccommodations extends ListRecords
{
    protected static string $resource = PropertyAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Accommodations'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(fn () => $this->getModel()::where('is_active', true)->count()),
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge(fn () => $this->getModel()::where('is_active', false)->count()),
            'available' => Tab::make('Available Now')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                          ->whereDoesntHave('reservations', function ($q) {
                              $q->where('check_in_date', '<=', now())
                                ->where('check_out_date', '>=', now())
                                ->whereIn('status', ['confirmed', 'checked_in']);
                          })
                )
                ->badge(fn () => 
                    $this->getModel()::where('is_active', true)
                        ->whereDoesntHave('reservations', function ($q) {
                            $q->where('check_in_date', '<=', now())
                              ->where('check_out_date', '>=', now())
                              ->whereIn('status', ['confirmed', 'checked_in']);
                        })->count()
                ),
            'occupied' => Tab::make('Currently Occupied')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('reservations', function ($q) {
                        $q->where('check_in_date', '<=', now())
                          ->where('check_out_date', '>=', now())
                          ->whereIn('status', ['confirmed', 'checked_in']);
                    })
                )
                ->badge(fn () => 
                    $this->getModel()::whereHas('reservations', function ($q) {
                        $q->where('check_in_date', '<=', now())
                          ->where('check_out_date', '>=', now())
                          ->whereIn('status', ['confirmed', 'checked_in']);
                    })->count()
                ),
        ];
    }
}
