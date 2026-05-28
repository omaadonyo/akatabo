<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'receipts';

    protected static ?string $recordTitleAttribute = 'number';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoice.number')
                    ->label('From Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'issued' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'cancelled' => 'Cancelled',
                    ]),
            ]);
    }
}
