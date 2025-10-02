<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyDeleteRequestResource\Pages;
use App\Filament\Resources\PropertyDeleteRequestResource\RelationManagers;
use App\Models\PropertyDeleteRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class PropertyDeleteRequestResource extends Resource
{
    protected static ?string $model = PropertyDeleteRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-trash';
    
    protected static ?string $navigationLabel = 'Deleted Properties';
    
    protected static ?string $modelLabel = 'Deleted Property';
    
    protected static ?string $pluralModelLabel = 'Deleted Properties';
    
    protected static ?string $navigationGroup = 'Property Management';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->relationship('property', 'name')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->disabled(),
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for Deletion')
                            ->disabled()
                            ->rows(3),
                        Forms\Components\DateTimePicker::make('requested_at')
                            ->disabled(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Admin Action')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3),
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->disabled(),
                        Forms\Components\Select::make('processed_by')
                            ->relationship('processedBy', 'name')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['property' => function ($query) {
                $query->withTrashed();
            }]))
            ->columns([
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processedBy.name')
                    ->label('Processed By')
                    ->placeholder('Not processed'),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed')
                    ->dateTime()
                    ->placeholder('Not processed'),
                Tables\Columns\IconColumn::make('can_delete')
                    ->label('Can Delete')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(function ($record) {
                        return $record->property ? $record->property->canBeDeleted() : false;
                    })
                    ->tooltip(function ($record) {
                        if (!$record->property) {
                            return 'Property not found';
                        }
                        return $record->property->canBeDeleted() 
                            ? 'No bookings - can be deleted' 
                            : 'Has bookings - cannot be deleted';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PropertyDeleteRequest $record) => $record->isPending())
                    ->disabled(fn (PropertyDeleteRequest $record) => !$record->property || !$record->property->canBeDeleted())
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional)')
                            ->rows(3),
                        Forms\Components\Placeholder::make('booking_warning')
                            ->label('Warning')
                            ->content(function (PropertyDeleteRequest $record) {
                                if (!$record->property) {
                                    return 'Property not found - cannot be deleted.';
                                }
                                return !$record->property->canBeDeleted() 
                                    ? 'This property has existing bookings and cannot be deleted.' 
                                    : 'This property has no bookings and can be safely deleted.';
                            })
                            ->visible(fn (PropertyDeleteRequest $record) => !$record->property || !$record->property->canBeDeleted()),
                    ])
                    ->action(function (PropertyDeleteRequest $record, array $data) {
                        try {
                            $record->approve(auth()->id(), $data['admin_notes'] ?? null);
                            
                            Notification::make()
                                ->title('Property delete request approved and property deleted')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Cannot approve deletion')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (PropertyDeleteRequest $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PropertyDeleteRequest $record, array $data) {
                        $record->reject(auth()->id(), $data['admin_notes']);
                        
                        Notification::make()
                            ->title('Property delete request rejected')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
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
            'index' => Pages\ListPropertyDeleteRequests::route('/'),
            'edit' => Pages\EditPropertyDeleteRequest::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : 'primary';
    }
}
