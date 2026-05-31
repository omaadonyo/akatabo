<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\UserResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $tenant = Filament::getTenant();
        if ($tenant) {
            $this->getRecord()->companies()->syncWithoutDetaching([$tenant->id]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
