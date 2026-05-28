<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Filament\Widgets\CompanyStatsOverview;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\TableWidget;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
            CompanyStatsOverview::class,
            RevenueChart::class,
            InvoiceStatusChart::class,
            MonthlyTrendChart::class,
            RecentInvoicesTable::class,
            LowStockTable::class,
        ];
    }

    public function getColumns(): array | int
    {
        return 3;
    }
}

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $totalInvoiced = Invoice::whereNotIn('status', ['draft', 'cancelled'])->sum('total');
        $totalPaid = Invoice::sum('paid_amount');
        $outstanding = $totalInvoiced - $totalPaid;
        $invoiceCount = Invoice::whereNotIn('status', ['cancelled'])->count();
        $quotationCount = Quotation::whereNotIn('status', ['draft', 'cancelled'])->count();
        $productCount = Product::where('type', 'product')->count();
        $lowStockCount = Product::all()->filter->isLowStock()->count();
        $companyCount = Company::count();
        $activeCompanyCount = Company::where('active', true)->count();

        return [
            Stat::make('Invoices', $invoiceCount)
                ->description($totalInvoiced > 0 ? '$' . number_format($totalInvoiced, 2) . ' total' : 'No invoices')
                ->icon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Outstanding', '$' . number_format(max(0, $outstanding), 2))
                ->description('$' . number_format($totalPaid, 2) . ' collected')
                ->icon('heroicon-o-currency-dollar')
                ->color($outstanding > 0 ? 'warning' : 'success'),
            Stat::make('Companies', $companyCount)
                ->description($activeCompanyCount . ' active')
                ->icon('heroicon-o-building-library')
                ->color('primary'),
            Stat::make('Quotations', $quotationCount)
                ->description(Quotation::where('status', 'accepted')->count() . ' accepted')
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Stat::make('Products', $productCount)
                ->description($lowStockCount > 0 ? $lowStockCount . ' low stock' : 'All stocked')
                ->icon('heroicon-o-archive-box')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 12 Months)';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = (float) Invoice::whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

class InvoiceStatusChart extends ChartWidget
{
    protected ?string $heading = 'Invoice Status';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statuses = Invoice::selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $colors = [
            'draft' => '#6b7280',
            'sent' => '#f59e0b',
            'paid' => '#16a34a',
            'overdue' => '#dc2626',
            'cancelled' => '#9ca3af',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Invoices',
                    'data' => $statuses->values()->toArray(),
                    'backgroundColor' => collect($statuses->keys())->map(fn ($s) => $colors[$s] ?? '#6b7280')->toArray(),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $statuses->keys()->map(fn ($s) => ucfirst($s))->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

class MonthlyTrendChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Comparison';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;
        $labels = [];
        $currentData = [];
        $previousData = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $i + 1;
            $labels[] = now()->month($month)->format('M');

            $currentData[] = (float) Invoice::whereMonth('date', $month)
                ->whereYear('date', $currentYear)
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->sum('total');

            $previousData[] = (float) Invoice::whereMonth('date', $month)
                ->whereYear('date', $previousYear)
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => (string) $currentYear,
                    'data' => $currentData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => (string) $previousYear,
                    'data' => $previousData,
                    'backgroundColor' => 'rgba(156, 163, 175, 0.1)',
                    'borderColor' => '#9ca3af',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

class RecentInvoicesTable extends TableWidget
{
    protected static ?string $heading = 'Recent Invoices';
    protected static ?int $sort = 4;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Invoice::query()->with('company')->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('number'),
            \Filament\Tables\Columns\TextColumn::make('company.name'),
            \Filament\Tables\Columns\TextColumn::make('total')->money('USD'),
            \Filament\Tables\Columns\TextColumn::make('status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray', 'sent' => 'warning', 'paid' => 'success',
                    'overdue' => 'danger', 'cancelled' => 'info', default => 'gray',
                }),
            \Filament\Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ];
    }
}

class LowStockTable extends TableWidget
{
    protected static ?string $heading = 'Low Stock Products';
    protected static ?int $sort = 5;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()
            ->where('type', 'product')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->whereNotNull('low_stock_threshold')
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('name'),
            \Filament\Tables\Columns\TextColumn::make('sku'),
            \Filament\Tables\Columns\TextColumn::make('stock_quantity'),
            \Filament\Tables\Columns\TextColumn::make('low_stock_threshold'),
        ];
    }
}
