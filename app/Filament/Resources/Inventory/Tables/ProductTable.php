<?php

namespace App\Filament\Resources\Inventory\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->width(48)
                    ->height(48)
                    ->square()
                    ->defaultImageUrl(fn ($record) => $record->isService()
                        ? 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="%23e5e7eb"><rect width="48" height="48" rx="4"/><text x="24" y="28" text-anchor="middle" font-size="12" fill="%239ca3af">SV</text></svg>')
                        : 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="%23e5e7eb"><rect width="48" height="48" rx="4"/><text x="24" y="28" text-anchor="middle" font-size="12" fill="%239ca3af">PD</text></svg>')
                    ),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'product' ? 'primary' : 'gray'),

                TextColumn::make('unit')
                    ->label('Unit'),

                TextColumn::make('unit_price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn ($record): ?string =>
                        $record && !$record->isService() && $record->isLowStock() ? 'danger' : null
                    )
                    ->formatStateUsing(fn ($record, $state) =>
                        $record && $record->isService() ? '—' : ($state ?? 0)
                    ),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'product' => 'Product',
                        'service' => 'Service',
                    ]),
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
