<?php

namespace App\Filament\Resources\Invoice\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $totalAll = Invoice::whereNotIn('status', ['cancelled'])->sum('total');
        $totalPaid = Invoice::sum('paid_amount');
        $outstanding = $totalAll - $totalPaid;

        $countByStatus = Invoice::selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $overdueCount = Invoice::where('status', 'overdue')->count();
        $paidCount = $countByStatus['paid'] ?? 0;
        $sentCount = $countByStatus['sent'] ?? 0;

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
                ->description(Invoice::where('status', 'overdue')->sum('total') > 0
                    ? '$' . number_format(Invoice::where('status', 'overdue')->sum('total'), 2) . ' total'
                    : 'No overdue invoices')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($overdueCount > 0 ? 'danger' : 'gray'),
            Stat::make('Average Invoice', '$' . number_format(
                $totalAll > 0 ? $totalAll / max(1, Invoice::whereNotIn('status', ['cancelled'])->count()) : 0, 2))
                ->description('Per invoice')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}
