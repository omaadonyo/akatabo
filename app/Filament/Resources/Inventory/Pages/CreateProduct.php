<?php

namespace App\Filament\Resources\Inventory\Pages;

use App\Filament\Resources\Inventory\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->id;

        if (empty($data['sku'])) {
            $data['sku'] = 'SKU-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8));
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
