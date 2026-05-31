<?php

namespace App\Filament\Resources\Receipt;

use App\Filament\Resources\Receipt\Pages\CreateReceipt;
use App\Filament\Resources\Receipt\Pages\EditReceipt;
use App\Filament\Resources\Receipt\Pages\ListReceipts;
use App\Filament\Resources\Receipt\Schemas\ReceiptForm;
use App\Filament\Resources\Receipt\Tables\ReceiptTable;
use App\Models\Receipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ReceiptPercent;

    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'number';

    public static function getNavigationBadge(): ?string
    {
        $tenantId = filament()->getTenant()?->id;
        if (!$tenantId) return null;
        return (string) static::getModel()::where('company_id', $tenantId)->count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return ReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceiptTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceipts::route('/'),
            'create' => CreateReceipt::route('/create'),
            'edit' => EditReceipt::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'company.name', 'customer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Client' => $record->customer?->name ?? $record->company?->name ?? '—',
            'Date' => $record->date?->format('M d, Y') ?? '—',
            'Total' => 'UGX ' . number_format($record->total, 2),
        ];
    }
}
