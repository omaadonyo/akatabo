<?php

namespace App\Filament\Resources\Quotation\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Quotation\QuotationResource;

class ViewQuotation extends ViewRecord
{
    protected static string $resource = QuotationResource::class;

    public function getView(): string
    {
        return 'filament.resources.quotation.view-quotation';
    }
}
