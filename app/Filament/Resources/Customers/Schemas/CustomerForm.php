<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Customer Information')
                    ->schema([

                        Grid::make(2)->schema([

                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),

                            Textarea::make('address')
                                ->label('Address')
                                ->rows(3)
                                ->autosize(),

                        ]),

                    ]),

            ]);
    }
}
