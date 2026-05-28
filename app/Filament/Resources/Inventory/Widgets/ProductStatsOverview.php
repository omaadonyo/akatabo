<?php

namespace App\Filament\Resources\Inventory\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $totalProducts = Product::where('type', 'product')->count();
        $totalServices = Product::where('type', 'service')->count();
        $lowStockCount = Product::all()->filter->isLowStock()->count();
        $activeCount = Product::where('is_active', true)->count();
        $inactiveCount = Product::where('is_active', false)->count();

        $productValue = Product::where('type', 'product')
            ->get()
            ->sum(fn ($p) => ($p->stock_quantity ?? 0) * ($p->unit_price ?? 0));

        return [
            Stat::make('Products', $totalProducts)
                ->description($activeCount . ' active, ' . $inactiveCount . ' inactive')
                ->icon('heroicon-o-cube')
                ->color('primary'),
            Stat::make('Services', $totalServices)
                ->description('Service offerings')
                ->icon('heroicon-o-cog')
                ->color('info'),
            Stat::make('Low Stock Alerts', $lowStockCount)
                ->description($lowStockCount > 0 ? 'Products below threshold' : 'All products well stocked')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
            Stat::make('Inventory Value', '$' . number_format($productValue, 2))
                ->description('Total stock at unit price')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
