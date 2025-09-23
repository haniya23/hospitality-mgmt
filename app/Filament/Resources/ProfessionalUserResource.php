<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionalUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProfessionalUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Professional Users';
    protected static ?string $navigationGroup = 'User Analytics';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('subscription_status', 'professional')->where('is_admin', false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('mobile_number')->required(),
                Forms\Components\TextInput::make('email'),
                Forms\Components\DateTimePicker::make('subscription_ends_at'),
                Forms\Components\TextInput::make('properties_limit')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('mobile_number')->searchable(),
                Tables\Columns\TextColumn::make('properties_count')
                    ->counts('properties')
                    ->label('Properties'),
                Tables\Columns\TextColumn::make('properties_limit')
                    ->label('Limit'),
                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_days')
                    ->getStateUsing(fn (User $record) => max(0, $record->subscription_ends_at ? $record->subscription_ends_at->diffInDays() : 0))
                    ->badge()
                    ->color(fn ($state) => $state <= 7 ? 'danger' : ($state <= 30 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('revenue')
                    ->getStateUsing(fn (User $record) => '₹699/month')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('b2b_partners_count')
                    ->counts('b2bPartnerContacts')
                    ->label('B2B Partners'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('subscription_ends_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfessionalUsers::route('/'),
            'create' => Pages\CreateProfessionalUser::route('/create'),
            'edit' => Pages\EditProfessionalUser::route('/{record}/edit'),
        ];
    }
}