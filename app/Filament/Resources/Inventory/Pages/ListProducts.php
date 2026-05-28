<?php

namespace App\Filament\Resources\Inventory\Pages;

use App\Filament\Resources\Inventory\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Inventory\Widgets\ProductStatsOverview::class,
        ];
    }
}
