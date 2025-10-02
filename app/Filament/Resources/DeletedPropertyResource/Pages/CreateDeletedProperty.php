<?php

namespace App\Filament\Resources\DeletedPropertyResource\Pages;

use App\Filament\Resources\DeletedPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeletedProperty extends CreateRecord
{
    protected static string $resource = DeletedPropertyResource::class;
}
