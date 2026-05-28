<?php

namespace App\Filament\Resources\Quotation\Pages;

use App\Filament\Resources\Quotation\QuotationResource;
use App\Models\Quotation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Quotation::withoutTrashed()->count()),
            'draft' => Tab::make('Draft')
                ->badge(Quotation::withoutTrashed()->where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
            'sent' => Tab::make('Sent')
                ->badge(Quotation::withoutTrashed()->where('status', 'sent')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent')),
            'accepted' => Tab::make('Accepted')
                ->badge(Quotation::withoutTrashed()->where('status', 'accepted')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'accepted')),
            'rejected' => Tab::make('Rejected')
                ->badge(Quotation::withoutTrashed()->where('status', 'rejected')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Quotation::withoutTrashed()->where('status', 'cancelled')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
