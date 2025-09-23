<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrialUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrialUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Trial Users';
    protected static ?string $navigationGroup = 'User Analytics';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('subscription_status', 'trial')->where('is_admin', false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('mobile_number')->required(),
                Forms\Components\TextInput::make('email'),
                Forms\Components\Select::make('trial_plan')
                    ->options(['starter' => 'Starter', 'professional' => 'Professional']),
                Forms\Components\DateTimePicker::make('trial_ends_at'),
                Forms\Components\Toggle::make('is_trial_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('mobile_number')->searchable(),
                Tables\Columns\TextColumn::make('trial_plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'starter' => 'success',
                        'professional' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('properties_count')
                    ->counts('properties')
                    ->label('Properties'),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_days')
                    ->getStateUsing(fn (User $record) => max(0, $record->trial_ends_at ? $record->trial_ends_at->diffInDays() : 0))
                    ->badge()
                    ->color(fn ($state) => $state <= 3 ? 'danger' : ($state <= 7 ? 'warning' : 'success')),
                Tables\Columns\IconColumn::make('is_trial_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trial_plan')
                    ->options(['starter' => 'Starter', 'professional' => 'Professional']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('trial_ends_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrialUsers::route('/'),
            'create' => Pages\CreateTrialUser::route('/create'),
            'edit' => Pages\EditTrialUser::route('/{record}/edit'),
        ];
    }
}