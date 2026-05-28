<?php

namespace App\Filament\Resources\Quotation\Schemas;

use App\Models\Customer;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('company_name')
                    ->default(fn () => auth()->user()?->currentTenant?->name),
                Hidden::make('company_address')
                    ->default(fn () => auth()->user()?->currentTenant?->address),
                Hidden::make('customer_name'),
                Hidden::make('customer_address'),
                Hidden::make('customer_email'),
                Hidden::make('subtotal'),
                Hidden::make('tax_amount'),
                Hidden::make('total'),

                Grid::make(12)->schema([

                    Group::make()
                        ->columnSpan(7)
                        ->schema([

                            Section::make('Details')
                                ->schema([

                                    Grid::make(2)->schema([

                                        Select::make('customer_id')
                                            ->label('Customer')
                                            ->options(fn () => Customer::where('company_id', auth()->user()?->currentTenant?->id)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, $set) {
                                                $customer = Customer::find($state);
                                                if ($customer) {
                                                    $set('customer_name', $customer->name);
                                                    $set('customer_address', $customer->address);
                                                    $set('customer_email', $customer->email);
                                                }
                                            }),

                                        TextInput::make('number')
                                            ->label('Quotation Number')
                                            ->placeholder('Auto-generated if left empty')
                                            ->disabled(fn ($operation) => $operation === 'edit'),

                                        DatePicker::make('date')
                                            ->label('Date')
                                            ->required()
                                            ->default(now()),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'sent' => 'Sent',
                                                'accepted' => 'Accepted',
                                                'rejected' => 'Rejected',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default('draft')
                                            ->required(),

                                    ]),

                                ]),

                            Section::make('Items')
                                ->schema([

                                    Repeater::make('items')
                                        ->relationship()
                                        ->schema([
                                            Grid::make(12)->schema([

                                                Select::make('product_id')
                                                    ->label('Product')
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(5)
                                                    ->relationship('product', 'name')
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        if (!$state) return;
                                                        $product = Product::find($state);
                                                        if ($product) {
                                                            $set('description', $product->name);
                                                            $set('unit', $product->unit);
                                                            $set('unit_price', $product->unit_price);
                                                            $qty = (float) ($get('quantity') ?? 1);
                                                            $price = (float) ($product->unit_price ?? 0);
                                                            $set('amount', number_format($qty * $price, 2, '.', ''));
                                                        }
                                                    }),

                                                TextInput::make('description')
                                                    ->label('Description')
                                                    ->columnSpan(7)
                                                    ->required(),

                                                TextInput::make('quantity')
                                                    ->label('Qty')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->reactive()
                                                    ->columnSpan(3)
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        $qty = (float) ($state ?? 0);
                                                        $price = (float) ($get('unit_price') ?? 0);
                                                        $set('amount', number_format($qty * $price, 2, '.', ''));
                                                    }),

                                                TextInput::make('unit')
                                                    ->label('Unit')
                                                    ->columnSpan(2)
                                                    ->placeholder('m, pcs...'),

                                                TextInput::make('unit_price')
                                                    ->label('Price')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->default(0)
                                                    ->reactive()
                                                    ->columnSpan(3)
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        $qty = (float) ($get('quantity') ?? 0);
                                                        $price = (float) ($state ?? 0);
                                                        $set('amount', number_format($qty * $price, 2, '.', ''));
                                                    }),

                                                TextInput::make('amount')
                                                    ->label('Amount')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->disabled()
                                                    ->columnSpan(4),

                                            ]),
                                        ])
                                        ->addActionLabel('Add Item')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            static::updateTotals($set, $get);
                                        })
                                        ->collapsible()
                                        ->defaultItems(1),

                                ]),

                            Section::make('Pricing')
                                ->schema([

                                    Grid::make(3)->schema([

                                        TextInput::make('discount')
                                            ->label('Discount')
                                            ->numeric()
                                            ->prefix('$')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                static::updateTotals($set, $get);
                                            }),

                                        TextInput::make('tax_rate')
                                            ->label('Tax Rate (%)')
                                            ->numeric()
                                            ->default(0)
                                            ->reactive()
                                            ->step(0.01)
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                static::updateTotals($set, $get);
                                            }),

                                    ]),

                                ]),

                            Section::make('Notes')
                                ->schema([

                                    Textarea::make('notes')
                                        ->label('Notes')
                                        ->autosize()
                                        ->rows(3)
                                        ->placeholder('Enter any additional notes...'),

                                ]),

                        ]),

                    Group::make()
                        ->columnSpan(5)
                        ->schema([

                            View::make('filament.forms.quotation-preview'),

                        ]),

                ]),

            ]);
    }

    public static function updateTotals($set, $get): void
    {
        $items = $get('items') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += (float) ($item['amount'] ?? 0);
        }

        $taxRate = (float) ($get('tax_rate') ?? 0);
        $discount = (float) ($get('discount') ?? 0);
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount - $discount;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('tax_amount', number_format($taxAmount, 2, '.', ''));
        $set('total', number_format(max(0, $total), 2, '.', ''));
    }
}
