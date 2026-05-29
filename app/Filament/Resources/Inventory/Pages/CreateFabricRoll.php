<?php

namespace App\Filament\Resources\Inventory\Pages;

use App\Filament\Resources\Inventory\FabricRollResource;
use App\Models\FabricRoll;
use Filament\Resources\Pages\CreateRecord;

class CreateFabricRoll extends CreateRecord
{
    protected static string $resource = FabricRollResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->id;

        if (blank($data['roll_code'] ?? null)) {
            $lastRoll = FabricRoll::withTrashed()
                ->where('roll_code', 'like', 'FLR-' . date('Y') . '-%')
                ->orderBy('roll_code', 'desc')
                ->first();

            if ($lastRoll) {
                $lastNumber = (int) substr($lastRoll->roll_code, -4);
                $data['roll_code'] = 'FLR-' . date('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $data['roll_code'] = 'FLR-' . date('Y') . '-0001';
            }
        }

        if (!isset($data['remaining_meters']) || blank($data['remaining_meters'])) {
            $data['remaining_meters'] = $data['verified_meters'] ?? 0;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
