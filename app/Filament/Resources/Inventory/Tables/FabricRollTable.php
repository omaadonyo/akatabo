<?php

namespace App\Filament\Resources\Inventory\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FabricRollTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('roll_code')
                    ->label('Roll Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fabric_name')
                    ->label('Fabric Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'white', 'black', 'gray', 'grey', 'silver' => 'gray',
                        'red', 'maroon' => 'danger',
                        'green', 'olive' => 'success',
                        'blue', 'navy', 'indigo' => 'info',
                        'yellow', 'gold', 'orange', 'amber' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('supplier')
                    ->label('Supplier')
                    ->searchable(),

                TextColumn::make('date_received')
                    ->label('Date Received')
                    ->date()
                    ->sortable(),

                TextColumn::make('verified_meters')
                    ->label('Verified')
                    ->suffix(' m')
                    ->sortable(),

                TextColumn::make('remaining_meters')
                    ->label('Remaining')
                    ->suffix(' m')
                    ->sortable()
                    ->color(fn ($record): ?string =>
                        $record && $record->remaining_meters <= ($record->verified_meters * 0.1) ? 'danger' :
                        ($record && $record->remaining_meters <= ($record->verified_meters * 0.3) ? 'warning' : null)
                    ),

                TextColumn::make('selling_price_per_meter')
                    ->label('Selling Price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'partially_used' => 'warning',
                        'depleted' => 'gray',
                        'damaged' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'partially_used' => 'Partially Used',
                        'depleted' => 'Depleted',
                        'damaged' => 'Damaged',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make()->slideOver()
                        ->modalContent(view('filament.resources.fabric-roll.view-fabric-roll')),
                    DeleteAction::make()
                        ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                ]),
            ])
            ->toolbarActions([
                Action::make('createUsage')
                    ->label('Record Usage')
                    ->icon(Heroicon::Scale)
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Select::make('fabric_roll_id')
                            ->label('Fabric Roll')
                            ->relationship('fabricRoll', 'roll_code')
                            ->searchable()
                            ->required(),
                        \Filament\Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->nullable(),
                        \Filament\Forms\Components\Select::make('invoice_id')
                            ->label('Invoice')
                            ->relationship('invoice', 'number')
                            ->searchable()
                            ->nullable(),
                        \Filament\Forms\Components\TextInput::make('meters_used')
                            ->label('Meters Used')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->suffix('m'),
                        \Filament\Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->default(now())
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->nullable(),
                    ])
                    ->action(function (array $data): void {
                        $roll = \App\Models\FabricRoll::findOrFail($data['fabric_roll_id']);
                        $usage = new \App\Models\FabricRollUsage();
                        $usage->fabric_roll_id = $roll->id;
                        $usage->company_id = filament()->getTenant()?->id ?? $roll->company_id;
                        $usage->customer_id = $data['customer_id'] ?? null;
                        $usage->invoice_id = $data['invoice_id'] ?? null;
                        $usage->meters_used = $data['meters_used'];
                        $usage->remaining_before = $roll->remaining_meters;
                        $usage->remaining_after = $roll->remaining_meters - $data['meters_used'];
                        $usage->date = $data['date'];
                        $usage->notes = $data['notes'] ?? null;
                        $usage->save();
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
