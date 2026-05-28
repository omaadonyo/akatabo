<?php

namespace App\Filament\Resources\Inventory;

use App\Filament\Resources\Inventory\Pages\CreateProduct;
use App\Filament\Resources\Inventory\Pages\EditProduct;
use App\Filament\Resources\Inventory\Pages\ListProducts;
use App\Filament\Resources\Inventory\Schemas\ProductForm;
use App\Filament\Resources\Inventory\Tables\ProductTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    protected static string | UnitEnum | null $navigationGroup = 'Inventory';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | HtmlString
    {
        $name = $record->name;

        if ($record->image) {
            $url = asset('storage/' . $record->image);
            return new HtmlString(
                '<span style="display:inline-flex;align-items:center;gap:8px;">' .
                '<img src="' . e($url) . '" style="width:22px;height:22px;border-radius:4px;object-fit:cover;flex-shrink:0;" alt="">' .
                '<span>' . e($name) . '</span>' .
                '</span>'
            );
        }

        return $name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'SKU' => $record->sku,
            'Type' => ucfirst($record->type),
            'Price' => $record->unit_price ? '$' . number_format($record->unit_price, 2) : null,
            'Stock' => $record->isService() ? '—' : number_format($record->stock_quantity, 0),
        ]);
    }
}
