<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            Grid::make(1)->schema([

                        FileUpload::make('logo')
                            ->label('Company Logo URL')
                            ->placeholder('Enter logo URL'),

                        TextInput::make('name')
                            ->label('Company Name')
                            ->required()
                            ->placeholder('Enter company name'),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->placeholder('Enter official email address'),

                        Textarea::make('address')
                            ->label('Business Address')
                            ->required()
                            ->autosize()
                            ->placeholder('Enter full business address'),
                
                        Textarea::make('invoice_notes')
                            ->label('Invoice Notes')
                            ->autosize()
                            ->placeholder('Enter notes to appear on invoices'),

                        Textarea::make('quotation_notes')
                            ->label('Quotation Notes')
                            ->autosize()
                            ->placeholder('Enter notes to appear on quotations'),

                        Textarea::make('receipt_notes')
                            ->label('Receipt Notes')
                            ->autosize()
                            ->placeholder('Enter notes to appear on receipts'),
            
            ]),
                
                     
                    
            ]);
    }
}
