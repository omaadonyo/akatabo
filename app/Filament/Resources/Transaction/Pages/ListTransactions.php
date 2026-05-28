<?php

namespace App\Filament\Resources\Transaction\Pages;

use App\Filament\Resources\Transaction\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;
}
