<?php

namespace App\Filament\Resources\CustomerDeposit;

use App\Filament\Resources\CustomerDeposit\Pages\CreateCustomerDeposit;
use App\Filament\Resources\CustomerDeposit\Pages\ListCustomerDeposits;
use App\Models\CustomerDeposit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CustomerDepositResource extends Resource
{
    protected static ?string $model = CustomerDeposit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name', fn ($query) => $query->where('company_id', filament()->getTenant()?->id))
                    ->searchable()
                    ->preload()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
                \Filament\Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'deposit' => 'Deposit',
                        'withdrawal' => 'Withdrawal',
                    ])
                    ->required(),
                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->autosize()
                    ->rows(3),
                \Filament\Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'deposit' => 'success',
                        'withdrawal' => 'danger',
                        'payment' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('reference_type')
                    ->label('Reference')
                    ->formatStateUsing(fn ($record) => $record->reference_type ? ucfirst($record->reference_type) . ' #' . $record->reference_id : '—'),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerDeposits::route('/'),
            'create' => CreateCustomerDeposit::route('/create'),
        ];
    }
}
