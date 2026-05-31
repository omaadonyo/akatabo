<?php

namespace App\Filament\Pages;

use App\Models\Company;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterCompany extends RegisterTenant
{
    protected static ?string $slug = 'new';

    public static function getLabel(): string
    {
        return 'Create Company';
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function handleRegistration(array $data): Company
    {
        $data['user_id'] = auth()->id();

        $company = Company::create($data);

        $company->users()->attach(auth()->id());

        return $company;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->placeholder('Enter official email address'),

                Textarea::make('address')
                    ->label('Business Address')
                    ->required()
                    ->autosize(),

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
