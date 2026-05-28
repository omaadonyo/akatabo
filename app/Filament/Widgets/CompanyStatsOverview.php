<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatsOverview extends StatsOverviewWidget
{
    protected function tenantId(): ?int
    {
        return auth()->user()?->currentTenant?->id;
    }

    protected function getCards(): array
    {
        $tenantId = $this->tenantId();

        $companiesQuery = Company::query()
            ->when($tenantId, fn ($q) => $q->where('id', $tenantId));

        $totalCompanies = (clone $companiesQuery)->count();
        $activeCompanies = (clone $companiesQuery)->where('active', true)->count();
        $inactiveCompanies = (clone $companiesQuery)->where('active', false)->count();

        $invoiceQuery = Invoice::query()->when($tenantId, fn ($q) => $q->where('company_id', $tenantId));
        $totalInvoiced = (clone $invoiceQuery)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total');
        $totalCollected = (clone $invoiceQuery)->sum('paid_amount');

        return [
            Stat::make('Total Companies', $totalCompanies)
                ->description($activeCompanies . ' active, ' . $inactiveCompanies . ' inactive')
                ->icon('heroicon-o-building-library')
                ->color('primary'),
            Stat::make('Invoiced', '$' . number_format($totalInvoiced, 2))
                ->description('$' . number_format($totalCollected, 2) . ' collected')
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Stat::make('Collected', '$' . number_format($totalCollected, 2))
                ->description(number_format($totalInvoiced > 0 ? ($totalCollected / $totalInvoiced) * 100 : 0, 1) . '% collection rate')
                ->icon('heroicon-o-receipt-percent')
                ->color('success'),
        ];
    }
}
