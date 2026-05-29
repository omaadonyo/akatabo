<?php

namespace App\Filament\Resources\Inventory\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FabricRollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            Section::make('Roll Information')
                ->description('Enter the basic details for this fabric roll.')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextInput::make('roll_code')
                                ->label('Roll Code')
                                ->placeholder('Auto-generated if left empty')
                                ->maxLength(255),

                            TextInput::make('fabric_name')
                                ->label('Fabric Name')
                                ->placeholder('e.g. Cotton Drill, Ankara, Linen')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('color')
                                ->label('Color')
                                ->placeholder('e.g. Navy Blue, Red, White')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('supplier')
                                ->label('Supplier')
                                ->placeholder('e.g. ABC Textiles Ltd')
                                ->required()
                                ->maxLength(255),

                            DatePicker::make('date_received')
                                ->label('Date Received')
                                ->default(now())
                                ->required(),

                            TextInput::make('fabric_width')
                                ->label('Fabric Width')
                                ->numeric()
                                ->suffix('cm')
                                ->step(0.01)
                                ->nullable(),

                        ]),

                ])
                ->columns(1),

            Section::make('Measurements & Pricing')
                ->description('Enter the measurement and pricing details.')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextInput::make('claimed_meters')
                                ->label('Claimed Meters')
                                ->numeric()
                                ->required()
                                ->suffix('m')
                                ->step(0.01),

                            TextInput::make('verified_meters')
                                ->label('Verified Meters')
                                ->numeric()
                                ->required()
                                ->suffix('m')
                                ->step(0.01)
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('remaining_meters', $state ?? 0);
                                }),

                            TextInput::make('buying_price_per_meter')
                                ->label('Buying Price / Meter')
                                ->numeric()
                                ->required()
                                ->prefix('$')
                                ->step(0.01),

                            TextInput::make('selling_price_per_meter')
                                ->label('Selling Price / Meter')
                                ->numeric()
                                ->required()
                                ->prefix('$')
                                ->step(0.01),

                            TextInput::make('remaining_meters')
                                ->label('Remaining Meters')
                                ->numeric()
                                ->suffix('m')
                                ->step(0.01)
                                ->default(fn ($get) => $get('verified_meters') ?? 0),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'in_stock' => 'In Stock',
                                    'partially_used' => 'Partially Used',
                                    'depleted' => 'Depleted',
                                    'damaged' => 'Damaged',
                                ])
                                ->default('in_stock')
                                ->required()
                                ->native(false),

                        ]),

                ]),

            Section::make('Notes')
                ->description('Additional notes about this fabric roll.')
                ->schema([

                    Textarea::make('notes')
                        ->label('Notes')
                        ->placeholder('Any additional information...')
                        ->autosize()
                        ->rows(3)
                        ->nullable(),

                ]),

            ]);
    }
}
