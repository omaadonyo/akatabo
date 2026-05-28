<?php

namespace App\Filament\Resources\Invoice\Pages;

use App\Filament\Resources\Invoice\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
}
