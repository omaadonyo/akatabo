<?php

namespace App\Filament\Resources\Inventory\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            Section::make('Product / Service Information')
    ->description('Enter the basic details for this product or service.')
    ->schema([

        Grid::make(2)
            ->schema([

                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Enter product or service name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sku')
                    ->label('SKU')
                    ->placeholder('Auto-generated if left empty')
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                Select::make('type')
                    ->label('Item Type')
                    ->options([
                        'product' => 'Product',
                        'service' => 'Service',
                    ])
                    ->default('product')
                    ->required()
                    ->reactive()
                    ->native(false)
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'service') {
                            $set('stock_quantity', null);
                            $set('low_stock_threshold', null);
                        }
                    }),

                Select::make('currency')
                    ->label('Currency')
                    ->options([
                        'UGX' => 'UGX — Uganda Shillings',
                        'USD' => 'USD — US Dollars',
                    ])
                    ->default('UGX')
                    ->required()
                    ->native(false),

            ]),

        Textarea::make('description')
            ->label('Description')
            ->placeholder('Add a short description...')
            ->autosize()
            ->rows(3)
            ->columnSpanFull(),

        FileUpload::make('image')
            ->label('Product Image')
            ->image()
            ->imageEditor()
            ->directory('products')
            ->nullable()
            ->columnSpanFull(),

    ])
    ->columns(1),

Section::make('Pricing & Units')
    ->description('Define pricing and measurement details.')
    ->schema([

        Grid::make(2)
            ->schema([

                TextInput::make('unit')
                    ->label('Unit')
                    ->placeholder('e.g. pcs, kg, m, hr, day, month')
                    ->maxLength(50),

                TextInput::make('unit_price')
                    ->label('Selling Price')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->prefix('UGX'),

                TextInput::make('buying_price')
                    ->label('Buying Price')
                    ->numeric()
                    ->default(0)
                    ->prefix('UGX')
                    ->helperText('Used for profit protection validation.'),

            ]),

    ]),

Section::make('Inventory & Status')
    ->description('Manage stock levels and item availability.')
    ->schema([

        Grid::make(2)
            ->schema([

                TextInput::make('stock_quantity')
                    ->label('Stock Quantity')
                    ->numeric()
                    ->default(0)
                    ->visible(fn ($get) => $get('type') === 'product')
                    ->helperText('Applicable only for products.'),

                TextInput::make('low_stock_threshold')
                    ->label('Low Stock Threshold')
                    ->numeric()
                    ->default(0)
                    ->visible(fn ($get) => $get('type') === 'product')
                    ->helperText('Receive alerts when stock falls below this quantity.'),

            ]),

        Toggle::make('is_active')
            ->label('Active Status')
            ->default(true)
            ->inline(false),

    ])

            ]);
    }
}
