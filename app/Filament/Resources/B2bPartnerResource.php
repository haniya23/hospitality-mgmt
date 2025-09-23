<?php

namespace App\Filament\Resources;

use App\Filament\Resources\B2bPartnerResource\Pages;
use App\Filament\Resources\B2bPartnerResource\RelationManagers;
use App\Models\B2bPartner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class B2bPartnerResource extends Resource
{
    protected static ?string $model = B2bPartner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('partner_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('partner_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('commission_rate')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('default_discount_pct')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('partnership_settings'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('requested_by')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('partnership_accepted_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_discount_pct')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('requested_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partnership_accepted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListB2bPartners::route('/'),
            'create' => Pages\CreateB2bPartner::route('/create'),
            'edit' => Pages\EditB2bPartner::route('/{record}/edit'),
        ];
    }
}
