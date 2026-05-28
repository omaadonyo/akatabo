<?php

namespace App\Filament\Resources\Transaction\Pages;

use App\Filament\Resources\Transaction\TransactionResource;
use App\Models\Transaction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Transaction::count()),
            'draft' => Tab::make('Draft')
                ->badge(Transaction::where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
            'sent' => Tab::make('Sent')
                ->badge(Transaction::where('status', 'sent')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent')),
            'paid' => Tab::make('Paid')
                ->badge(Transaction::where('status', 'paid')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            'accepted' => Tab::make('Accepted')
                ->badge(Transaction::where('status', 'accepted')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'accepted')),
            'issued' => Tab::make('Issued')
                ->badge(Transaction::where('status', 'issued')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'issued')),
            'overdue' => Tab::make('Overdue')
                ->badge(Transaction::where('status', 'overdue')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'overdue')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Transaction::where('status', 'cancelled')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
            'rejected' => Tab::make('Rejected')
                ->badge(Transaction::where('status', 'rejected')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}
