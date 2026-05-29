<?php

namespace App\Filament\Resources\CustomerDeposit\Pages;

use App\Filament\Resources\CustomerDeposit\CustomerDepositResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerDeposits extends ListRecords
{
    protected static string $resource = CustomerDepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
