<?php

namespace App\Filament\Resources\Transaction\Tables;

use App\Filament\Actions\ExportCsvBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'quotation' => 'gray',
                        'invoice' => 'warning',
                        'receipt' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('document_number')
                    ->label('Document')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'accepted' => 'success',
                        'paid' => 'success',
                        'issued' => 'success',
                        'rejected' => 'danger',
                        'overdue' => 'danger',
                        'cancelled' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'quotation' => 'Quotation',
                        'invoice' => 'Invoice',
                        'receipt' => 'Receipt',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'paid' => 'Paid',
                        'issued' => 'Issued',
                        'rejected' => 'Rejected',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportCsvBulkAction::make('exportCsv')
                        ->label('Export CSV')
                        ->columns([
                            'created_at' => 'Date',
                            'type' => 'Type',
                            'document_number' => 'Document',
                            'company.name' => 'Company',
                            'amount' => 'Amount',
                            'status' => 'Status',
                            'description' => 'Description',
                        ])
                        ->fileName('transactions-export.csv'),
                    \App\Filament\Actions\ExportPdfBulkAction::make('exportPdf')
                        ->label('Export PDF')
                        ->pdfView('pdf.bulk-transactions')
                        ->fileName('transactions-export.pdf'),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
