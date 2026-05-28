<?php

namespace App\Filament\Resources\Invoice\Pages;

use App\Filament\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Invoice\Widgets\InvoiceStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Invoice::withoutTrashed()->count()),
            'draft' => Tab::make('Draft')
                ->badge(Invoice::withoutTrashed()->where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
            'sent' => Tab::make('Sent')
                ->badge(Invoice::withoutTrashed()->where('status', 'sent')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent')),
            'paid' => Tab::make('Paid')
                ->badge(Invoice::withoutTrashed()->where('status', 'paid')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            'overdue' => Tab::make('Overdue')
                ->badge(Invoice::withoutTrashed()->where('status', 'overdue')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'overdue')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Invoice::withoutTrashed()->where('status', 'cancelled')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
