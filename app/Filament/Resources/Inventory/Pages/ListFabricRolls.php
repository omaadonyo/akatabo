<?php

namespace App\Filament\Resources\Inventory\Pages;

use App\Filament\Resources\Inventory\FabricRollResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFabricRolls extends ListRecords
{
    protected static string $resource = FabricRollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
