<?php

namespace App\Filament\Resources\Receipt\Tables;

use App\Filament\Actions\ExportCsvBulkAction;
use App\Filament\Resources\Receipt\Actions\DownloadPdfAction;
use App\Helpers\QrCodeHelper;
use App\Mail\ReceiptMail;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ReceiptTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoice.number')
                    ->label('From Invoice')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->default(fn ($record) => $record->company?->name),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('UGX')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'issued' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'cancelled' => 'Cancelled',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->slideOver()
                        ->modalContent(fn ($record) => view('filament.resources.receipt.view-receipt', [
                            'receipt' => $record,
                            'company' => $record?->company,
                            'customer' => $record?->customer,
                            'items' => $record?->items ?? collect(),
                            'qrSvg' => QrCodeHelper::generateSvg($record->public_url),
                        ]))
                        ->modalWidth('3xl'),
                    DownloadPdfAction::make(),
                    Action::make('emailReceipt')
                        ->label('Email Receipt')
                        ->icon('heroicon-o-envelope')
                        ->visible(fn ($record) => $record->status === 'issued')
                        ->action(function ($record) {
                            $company = $record->company;

                            if (!$company || !$company->email) {
                                Notification::make()
                                    ->title('No email address')
                                    ->body($company->name . ' does not have an email address configured.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            Mail::to($company->email)->send(new ReceiptMail($record));

                            Notification::make()
                                ->title('Email sent')
                                ->body('Receipt ' . $record->number . ' sent to ' . $company->email)
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportCsvBulkAction::make('exportCsv')
                        ->label('Export CSV')
                        ->columns([
                            'number' => 'Number',
                            'invoice.number' => 'From Invoice',
                            'customer.name' => 'Customer',
                            'date' => 'Date',
                            'subtotal' => 'Subtotal',
                            'tax_amount' => 'Tax',
                            'discount' => 'Discount',
                            'total' => 'Total',
                            'status' => 'Status',
                        ])
                        ->fileName('receipts-export.csv'),
                    \App\Filament\Actions\ExportPdfBulkAction::make('exportPdf')
                        ->label('Export PDF')
                        ->pdfView('pdf.bulk-receipts')
                        ->fileName('receipts-export.pdf'),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
