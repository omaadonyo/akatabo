<?php

namespace App\Filament\Resources\Inventory\Pages;

use App\Filament\Resources\Inventory\FabricRollResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditFabricRoll extends EditRecord
{
    protected static string $resource = FabricRollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
