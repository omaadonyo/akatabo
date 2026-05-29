<?php

namespace App\Filament\Widgets;

use App\Models\FabricRoll;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class LowFabricStockWidget extends TableWidget
{
    protected static ?string $heading = 'Low Fabric Stock';
    protected static ?int $sort = 6;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $tenantId = filament()->getTenant()?->id;

        return FabricRoll::query()
            ->where('remaining_meters', '>', 0)
            ->whereRaw('remaining_meters <= verified_meters * 0.1')
            ->when($tenantId, fn ($q) => $q->where('company_id', $tenantId))
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('roll_code')
                ->label('Roll Code')
                ->searchable(),

            TextColumn::make('fabric_name')
                ->label('Fabric'),

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

            TextColumn::make('remaining_meters')
                ->label('Remaining')
                ->suffix(' m')
                ->color('danger'),

            TextColumn::make('remaining_percentage')
                ->label('Remaining %')
                ->formatStateUsing(fn ($state): string => number_format($state, 1) . '%'),
        ];
    }
}
