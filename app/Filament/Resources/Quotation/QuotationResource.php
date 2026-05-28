<?php

namespace App\Filament\Resources\Quotation;

use App\Filament\Resources\Quotation\Pages\CreateQuotation;
use App\Filament\Resources\Quotation\Pages\EditQuotation;
use App\Filament\Resources\Quotation\Pages\ListQuotations;
use App\Filament\Resources\Quotation\Pages\ViewQuotation;
use App\Filament\Resources\Quotation\Schemas\QuotationForm;
use App\Filament\Resources\Quotation\Tables\QuotationTable;
use App\Models\Quotation;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'view' => ViewQuotation::route('/{record}'),
            'edit' => EditQuotation::route('/{record}/edit'),
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
            'Total' => '$' . number_format($record->total, 2),
        ];
    }
}
