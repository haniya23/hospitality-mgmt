<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Models\User;
use App\Models\PropertyCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Properties';
    protected static ?string $navigationGroup = 'Property Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_id')
                    ->label('Owner')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mobile_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Select::make('subscription_status')
                            ->options([
                                'trial' => 'Trial',
                                'starter' => 'Starter',
                                'professional' => 'Professional',
                            ])
                            ->default('trial')
                            ->required(),
                        Forms\Components\TextInput::make('properties_limit')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return User::create([
                            'name' => $data['name'],
                            'mobile_number' => $data['mobile_number'],
                            'email' => $data['email'],
                            'subscription_status' => $data['subscription_status'],
                            'properties_limit' => $data['properties_limit'],
                            'is_trial_active' => $data['subscription_status'] === 'trial',
                        ])->getKey();
                    }),
                Forms\Components\Select::make('property_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull()
                    ->rows(3)
                    ->visible(fn (Get $get) => $get('status') === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'active' => 'success',
                        'rejected' => 'danger',
                        'inactive' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'rejected' => 'Rejected',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Property $record) => $record->status === 'pending')
                    ->action(function (Property $record) {
                        $record->update([
                            'status' => 'active',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()
                            ->title('Property approved successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (Property $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Property $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()
                            ->title('Property rejected')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
