<?php

namespace App\Filament\Resources\Companies\Widgets;

use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('active', true)->count();
        $inactiveCompanies = Company::where('active', false)->count();

        $companiesWithInvoices = Company::whereHas('invoices')->count();
        $companiesWithReceipts = Company::whereHas('receipts')->count();
        $companiesWithQuotations = Company::whereHas('quotations')->count();

        $totalInvoiced = \App\Models\Invoice::whereHas('company', fn ($q) => $q->where('active', true))
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total');

        $totalCollected = \App\Models\Invoice::whereHas('company', fn ($q) => $q->where('active', true))
            ->sum('paid_amount');

        return [
            Stat::make('Total Companies', $totalCompanies)
                ->description($activeCompanies . ' active, ' . $inactiveCompanies . ' inactive')
                ->icon('heroicon-o-building-library')
                ->color('primary'),
            Stat::make('With Invoices', $companiesWithInvoices)
                ->description('UGX ' . number_format($totalInvoiced, 2) . ' invoiced')
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Stat::make('With Receipts', $companiesWithReceipts)
                ->description('UGX ' . number_format($totalCollected, 2) . ' collected')
                ->icon('heroicon-o-receipt-percent')
                ->color('success'),
            Stat::make('With Quotations', $companiesWithQuotations)
                ->description(Company::whereHas('quotations', fn ($q) => $q->where('status', 'accepted'))->count() . ' with accepted')
                ->icon('heroicon-o-document-text')
                ->color('warning'),
        ];
    }
}
