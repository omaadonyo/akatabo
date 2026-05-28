<?php

namespace App\Filament\Resources\Invoice\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceStatsOverview extends StatsOverviewWidget
{
    protected function tenantId(): ?int
    {
        return filament()->getTenant()?->id;
    }

    protected function getCards(): array
    {
        $tenantId = $this->tenantId();

        $baseQuery = Invoice::query()->when($tenantId, fn ($q) => $q->where('company_id', $tenantId));

        $totalAll = (clone $baseQuery)->whereNotIn('status', ['cancelled'])->sum('total');
        $totalPaid = (clone $baseQuery)->sum('paid_amount');
        $outstanding = $totalAll - $totalPaid;

        $countByStatus = (clone $baseQuery)
            ->selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $overdueCount = (clone $baseQuery)->where('status', 'overdue')->count();
        $paidCount = $countByStatus['paid'] ?? 0;
        $sentCount = $countByStatus['sent'] ?? 0;
        $totalCount = (clone $baseQuery)->whereNotIn('status', ['cancelled'])->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalAll, 2))
                ->description($paidCount . ' paid, ' . $sentCount . ' sent')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Outstanding', '$' . number_format(max(0, $outstanding), 2))
                ->description('$' . number_format($totalPaid, 2) . ' collected')
                ->icon('heroicon-o-clock')
                ->color($outstanding > 0 ? 'warning' : 'success'),
            Stat::make('Overdue', $overdueCount)
                ->description((clone $baseQuery)->where('status', 'overdue')->sum('total') > 0
                    ? '$' . number_format((clone $baseQuery)->where('status', 'overdue')->sum('total'), 2) . ' total'
                    : 'No overdue invoices')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($overdueCount > 0 ? 'danger' : 'gray'),
            Stat::make('Average Invoice', '$' . number_format(
                $totalAll > 0 ? $totalAll / max(1, $totalCount) : 0, 2))
                ->description('Per invoice')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}
