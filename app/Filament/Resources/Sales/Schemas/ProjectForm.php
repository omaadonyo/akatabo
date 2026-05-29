<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Details')
                    ->schema([

                        Grid::make(2)->schema([

                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255),

                            Select::make('customer_id')
                                ->label('Customer')
                                ->relationship('customer', 'name', fn ($query) => $query->where('company_id', filament()->getTenant()?->id))
                                ->searchable()
                                ->preload()
                                ->nullable(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'on_hold' => 'On Hold',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->default('active')
                                ->required(),

                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->nullable(),

                            DatePicker::make('end_date')
                                ->label('End Date')
                                ->nullable(),

                            TextInput::make('budget')
                                ->label('Budget')
                                ->numeric()
                                ->prefix('$')
                                ->default(0),

                        ]),

                    ]),

                Section::make('Notes')
                    ->schema([

                        Textarea::make('description')
                            ->label('Description')
                            ->autosize()
                            ->rows(3)
                            ->nullable(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->autosize()
                            ->rows(3)
                            ->nullable(),

                    ]),

            ]);
    }
}
