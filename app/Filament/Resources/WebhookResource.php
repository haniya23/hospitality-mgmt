<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebhookResource\Pages;
use App\Filament\Resources\WebhookResource\RelationManagers;
use App\Models\Webhook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    
    protected static ?string $navigationGroup = 'Subscriptions';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider')
                    ->options([
                        'cashfree' => 'Cashfree',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('event_id')
                    ->label('Event ID')
                    ->maxLength(255),
                Forms\Components\Textarea::make('payload')
                    ->label('Payload (JSON)')
                    ->rows(10)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('signature_header')
                    ->label('Signature Header')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('received_at')
                    ->required(),
                Forms\Components\Toggle::make('processed')
                    ->required(),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\Textarea::make('error_message')
                    ->label('Error Message')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cashfree' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('event_id')
                    ->label('Event ID')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('payload')
                    ->label('Event Type')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && isset($state['type'])) {
                            return $state['type'];
                        }
                        return 'Unknown';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PAYMENT_SUCCESS' => 'success',
                        'PAYMENT_FAILED' => 'danger',
                        'PAYMENT_USER_DROPPED' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('received_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('processed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(30)
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'cashfree' => 'Cashfree',
                    ]),
                Tables\Filters\TernaryFilter::make('processed')
                    ->label('Processing Status')
                    ->placeholder('All webhooks')
                    ->trueLabel('Processed')
                    ->falseLabel('Unprocessed'),
                Tables\Filters\Filter::make('with_errors')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('error_message'))
                    ->label('With Errors'),
                Tables\Filters\Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->where('received_at', '>=', now()->subHours(24)))
                    ->label('Last 24 hours'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('received_at', 'desc');
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
            'index' => Pages\ListWebhooks::route('/'),
            // No create/edit pages for webhooks
        ];
    }
}
