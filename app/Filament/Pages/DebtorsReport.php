<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DebtorsReport extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?string $title = 'Debtors Report';

    protected string $view = 'filament.pages.debtors-report';

    public ?array $filters = [];

    public function getTableQuery(): Builder
    {
        $tenantId = filament()->getTenant()?->id;

        return Customer::query()
            ->with('invoices')
            ->whereHas('invoices', function ($q) {
                $q->whereNotIn('status', ['draft', 'cancelled', 'paid'])
                    ->whereColumn('total', '>', 'paid_amount');
            })
            ->when($tenantId, fn ($q) => $q->where('company_id', $tenantId));
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Customer')
                ->searchable()
                ->sortable(),
            TextColumn::make('email')
                ->label('Email')
                ->searchable(),
            TextColumn::make('outstanding_balance')
                ->label('Total Outstanding')
                ->money('USD'),
            TextColumn::make('oldest_invoice_date')
                ->label('Oldest Invoice')
                ->date()
                ->getStateUsing(function ($record) {
                    $oldest = $record->invoices()
                        ->whereNotIn('status', ['draft', 'cancelled', 'paid'])
                        ->orderBy('date')
                        ->first();
                    return $oldest?->date;
                }),
            TextColumn::make('invoice_count')
                ->label('Invoice Count')
                ->getStateUsing(function ($record) {
                    return $record->invoices()
                        ->whereNotIn('status', ['draft', 'cancelled', 'paid'])
                        ->count();
                }),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            \Filament\Tables\Filters\Filter::make('date_range')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date'),
                    DatePicker::make('end_date')
                        ->label('End Date'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['start_date'], fn ($q, $date) => $q->whereHas('invoices', fn ($q) => $q->whereDate('date', '>=', $date)))
                        ->when($data['end_date'], fn ($q, $date) => $q->whereHas('invoices', fn ($q) => $q->whereDate('date', '<=', $date)));
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
}
