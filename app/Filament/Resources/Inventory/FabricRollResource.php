<?php

namespace App\Filament\Resources\Inventory;

use App\Filament\Resources\Inventory\Pages\CreateFabricRoll;
use App\Filament\Resources\Inventory\Pages\EditFabricRoll;
use App\Filament\Resources\Inventory\Pages\ListFabricRolls;
use App\Filament\Resources\Inventory\Schemas\FabricRollForm;
use App\Filament\Resources\Inventory\Tables\FabricRollTable;
use App\Models\FabricRoll;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;

class FabricRollResource extends Resource
{
    protected static ?string $model = FabricRoll::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ViewfinderCircle;

    protected static string | UnitEnum | null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Fabric Rolls';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'roll_code';

    public static function form(Schema $schema): Schema
    {
        return FabricRollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FabricRollTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFabricRolls::route('/'),
            'create' => CreateFabricRoll::route('/create'),
            'edit' => EditFabricRoll::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['roll_code', 'fabric_name', 'color', 'supplier'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | HtmlString
    {
        return $record->roll_code;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Fabric' => $record->fabric_name,
            'Color' => $record->color,
            'Supplier' => $record->supplier,
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'in_stock')->count();
    }
}
