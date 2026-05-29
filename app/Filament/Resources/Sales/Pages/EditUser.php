<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->isAdmin() ?? false),
        ];
    }
}
