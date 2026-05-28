<?php

namespace App\Filament\Resources\Receipt\Pages;

use App\Filament\Resources\Receipt\ReceiptResource;
use App\Models\Receipt;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;

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
                ->badge(Receipt::withoutTrashed()->count()),
            'issued' => Tab::make('Issued')
                ->badge(Receipt::withoutTrashed()->where('status', 'issued')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'issued')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Receipt::withoutTrashed()->where('status', 'cancelled')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
