<?php

namespace App\Filament\Resources\Inventory\Actions;

use App\Models\FabricRoll;
use App\Models\FabricRollUsage;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class CreateFabricRollUsageAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Record Fabric Usage')
            ->icon('heroicon-o-scale')
            ->color('warning')
            ->modalHeading('Record Fabric Usage')
            ->modalDescription('Log how much fabric was cut/used from this roll.')
            ->form(function (array $arguments) {
                $roll = isset($arguments['fabric_roll_id'])
                    ? FabricRoll::find($arguments['fabric_roll_id'])
                    : null;

                return [
                    TextInput::make('fabric_roll_id')
                        ->label('Fabric Roll')
                        ->disabled()
                        ->dehydrated()
                        ->default($roll?->roll_code ?? $arguments['fabric_roll_id'] ?? ''),

                    TextInput::make('roll_id')
                        ->hidden()
                        ->default($roll?->id ?? $arguments['fabric_roll_id'] ?? ''),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->nullable(),

                    Select::make('invoice_id')
                        ->label('Invoice')
                        ->relationship('invoice', 'number')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('meters_used')
                        ->label('Meters Used')
                        ->required()
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(fn () => $roll ? $roll->remaining_meters : 999999)
                        ->suffix('m'),

                    DatePicker::make('date')
                        ->label('Date')
                        ->default(now())
                        ->required(),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->nullable(),
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $roll = FabricRoll::findOrFail($arguments['fabric_roll_id']);

                $usage = new FabricRollUsage();
                $usage->fabric_roll_id = $roll->id;
                $usage->company_id = Filament::getTenant()?->id ?? $roll->company_id;
                $usage->customer_id = $data['customer_id'] ?? null;
                $usage->invoice_id = $data['invoice_id'] ?? null;
                $usage->meters_used = $data['meters_used'];
                $usage->remaining_before = $roll->remaining_meters;
                $usage->remaining_after = $roll->remaining_meters - $data['meters_used'];
                $usage->date = $data['date'];
                $usage->notes = $data['notes'] ?? null;
                $usage->save();
            });
    }
}
