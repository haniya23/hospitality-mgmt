<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeletedPropertyResource\Pages;
use App\Filament\Resources\DeletedPropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class DeletedPropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    
    protected static ?string $navigationLabel = 'Deleted Properties';
    
    protected static ?string $modelLabel = 'Deleted Property';
    
    protected static ?string $pluralModelLabel = 'Deleted Properties';
    
    protected static ?string $navigationGroup = 'Property Management';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()->with(['owner', 'category', 'location.city.district.state', 'propertyAccommodations']))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Property Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.city.name')
                    ->label('Location')
                    ->formatStateUsing(function ($record) {
                        if ($record->location && $record->location->city) {
                            return $record->location->city->name . ', ' . $record->location->city->district->state->name;
                        }
                        return 'No location';
                    }),
                Tables\Columns\TextColumn::make('property_accommodations_count')
                    ->label('Accommodations')
                    ->counts('propertyAccommodations'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_restore')
                    ->label('Can Restore')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(function ($record) {
                        return $record->owner->canCreateProperty();
                    })
                    ->tooltip(function ($record) {
                        return $record->owner->canCreateProperty() 
                            ? 'User can restore this property' 
                            : 'User has reached property limit';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('owner')
                    ->relationship('owner', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->disabled(fn (Property $record) => !$record->owner->canCreateProperty())
                    ->form([
                        Forms\Components\Section::make('Property Information')
                            ->schema([
                                Forms\Components\Placeholder::make('property_name')
                                    ->label('Property Name')
                                    ->content(fn (Property $record) => $record->name),
                                Forms\Components\Placeholder::make('owner_name')
                                    ->label('Owner')
                                    ->content(fn (Property $record) => $record->owner->name),
                                Forms\Components\Placeholder::make('accommodations_count')
                                    ->label('Property Accommodations')
                                    ->content(fn (Property $record) => $record->propertyAccommodations->count() . ' accommodations'),
                            ])->columns(3),
                            
                        Forms\Components\Section::make('Current User Status')
                            ->schema([
                                Forms\Components\Placeholder::make('current_properties')
                                    ->label('Current Properties')
                                    ->content(fn (Property $record) => $record->owner->properties()->count() . ' properties'),
                                Forms\Components\Placeholder::make('current_accommodations')
                                    ->label('Current Accommodations')
                                    ->content(function (Property $record) {
                                        $totalAccommodations = $record->owner->properties()
                                            ->withCount('propertyAccommodations')
                                            ->get()
                                            ->sum('property_accommodations_count');
                                        return $totalAccommodations . ' accommodations';
                                    }),
                                Forms\Components\Placeholder::make('subscription_plan')
                                    ->label('Subscription Plan')
                                    ->content(function (Property $record) {
                                        $subscription = $record->owner->subscriptions()->active()->first();
                                        return $subscription ? $subscription->name : 'Free Plan';
                                    }),
                            ])->columns(3),
                            
                        Forms\Components\Section::make('Subscription Limits')
                            ->schema([
                                Forms\Components\Placeholder::make('property_limit')
                                    ->label('Property Limit')
                                    ->content(function (Property $record) {
                                        $limit = $record->owner->getPropertyLimit();
                                        return $limit === -1 ? 'Unlimited' : $limit . ' properties';
                                    }),
                                Forms\Components\Placeholder::make('accommodation_limit')
                                    ->label('Accommodation Limit')
                                    ->content(function (Property $record) {
                                        $limit = $record->owner->getAccommodationLimit();
                                        return $limit === -1 ? 'Unlimited' : $limit . ' accommodations';
                                    }),
                                Forms\Components\Placeholder::make('can_restore_status')
                                    ->label('Restore Status')
                                    ->content(function (Property $record) {
                                        if (!$record->owner->canCreateProperty()) {
                                            return '❌ Cannot restore - property limit reached';
                                        }
                                        
                                        $currentAccommodations = $record->owner->properties()
                                            ->withCount('propertyAccommodations')
                                            ->get()
                                            ->sum('property_accommodations_count');
                                        $propertyAccommodations = $record->propertyAccommodations->count();
                                        $accommodationLimit = $record->owner->getAccommodationLimit();
                                        
                                        if ($accommodationLimit !== -1 && ($currentAccommodations + $propertyAccommodations) > $accommodationLimit) {
                                            return '❌ Cannot restore - accommodation limit would be exceeded';
                                        }
                                        
                                        return '✅ Can be restored';
                                    }),
                            ])->columns(3),
                            
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional)')
                            ->rows(3),
                    ])
                    ->action(function (Property $record, array $data) {
                        try {
                            // Validate subscription limits
                            if (!$record->owner->canCreateProperty()) {
                                throw new \Exception('User has reached their property limit');
                            }
                            
                            $currentAccommodations = $record->owner->properties()
                                ->withCount('propertyAccommodations')
                                ->get()
                                ->sum('property_accommodations_count');
                            $propertyAccommodations = $record->propertyAccommodations->count();
                            $accommodationLimit = $record->owner->getAccommodationLimit();
                            
                            if ($accommodationLimit !== -1 && ($currentAccommodations + $propertyAccommodations) > $accommodationLimit) {
                                throw new \Exception('Restoring this property would exceed the accommodation limit');
                            }
                            
                            // Restore the property
                            $record->restore();
                            
                            Notification::make()
                                ->title('Property restored successfully')
                                ->body("Property '{$record->name}' has been restored for {$record->owner->name}")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Cannot restore property')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('force_delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Property')
                    ->modalDescription('This will permanently delete the property and all its data. This action cannot be undone.')
                    ->action(function (Property $record) {
                        $record->forceDelete();
                        
                        Notification::make()
                            ->title('Property permanently deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('restore_selected')
                        ->label('Restore Selected')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $restored = 0;
                            $failed = 0;
                            
                            foreach ($records as $record) {
                                try {
                                    if ($record->owner->canCreateProperty()) {
                                        $record->restore();
                                        $restored++;
                                    } else {
                                        $failed++;
                                    }
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }
                            
                            Notification::make()
                                ->title("Restored {$restored} properties" . ($failed > 0 ? ", {$failed} failed" : ""))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('deleted_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeletedProperties::route('/'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::onlyTrashed()->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'danger' : 'primary';
    }
}
