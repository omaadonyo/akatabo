<?php

namespace App\Filament\Resources\Transaction;

use App\Filament\Resources\Transaction\Pages\ListTransactions;
use App\Filament\Resources\Transaction\Tables\TransactionTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowTrendingUp;

    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'document_number';

    public static function getNavigationBadge(): ?string
    {
        $tenantId = filament()->getTenant()?->id;
        if (!$tenantId) return null;
        return (string) static::getModel()::where('company_id', $tenantId)->count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'gray';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return TransactionTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
        ];
    }
}
