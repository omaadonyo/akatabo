<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class CompanySettings extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Company Settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('logo')
                    ->label('Company Logo')
                    ->image()
                    ->nullable(),

                TextInput::make('name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email(),

                Textarea::make('address')
                    ->label('Business Address')
                    ->required()
                    ->autosize(),

                Toggle::make('active')
                    ->label('Active')
                    ->default(false)
                    ->inline(false),

                Textarea::make('invoice_notes')
                    ->label('Invoice Notes')
                    ->autosize()
                    ->columnSpanFull(),

                Textarea::make('quotation_notes')
                    ->label('Quotation Notes')
                    ->autosize()
                    ->columnSpanFull(),

                Textarea::make('receipt_notes')
                    ->label('Receipt Notes')
                    ->autosize()
                    ->columnSpanFull(),
            ]);
    }
}
