<?php

namespace App\Filament\Resources\Invoice;

use App\Filament\Resources\Invoice\Pages\CreateInvoice;
use App\Filament\Resources\Invoice\Pages\EditInvoice;
use App\Filament\Resources\Invoice\Pages\ListInvoices;
use App\Filament\Resources\Invoice\Schemas\InvoiceForm;
use App\Filament\Resources\Invoice\Tables\InvoiceTable;
use App\Models\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoiceTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit' => EditInvoice::route('/{record}/edit'),
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
            'Balance' => '$' . number_format($record->balance, 2),
        ];
    }
}
