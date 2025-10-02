<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('bulk_import')
                ->label('Bulk Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->url(static::getResource()::getUrl('create') . '#bulk-import')
                ->tooltip('Import locations from CSV files'),
        ];
    }
}
