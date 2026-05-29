<?php

namespace App\Filament\Resources\CustomerDeposit\Pages;

use App\Filament\Resources\CustomerDeposit\CustomerDepositResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerDeposit extends CreateRecord
{
    protected static string $resource = CustomerDepositResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->id;

        return $data;
    }
}
