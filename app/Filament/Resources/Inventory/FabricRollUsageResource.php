<?php

namespace App\Filament\Resources\Inventory;

use App\Filament\Resources\Inventory\Pages\ListFabricRollUsages;
use App\Models\FabricRollUsage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;

class FabricRollUsageResource extends Resource
{
    protected static ?string $model = FabricRollUsage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Scale;

    protected static string | UnitEnum | null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Fabric Usage';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fabricRoll.roll_code')
                    ->label('Roll Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('meters_used')
                    ->label('Meters Used')
                    ->suffix(' m')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('fabric_roll_id')
                    ->label('Fabric Roll')
                    ->relationship('fabricRoll', 'roll_code'),

                SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name'),

                \Filament\Tables\Filters\Filter::make('date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'], fn ($q) => $q->whereDate('date', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('date', '<=', $data['until']))),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFabricRollUsages::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | HtmlString
    {
        return 'Usage #' . $record->id;
    }
}
