<?php

namespace App\Filament\Resources\Invoice\Tables;

use App\Filament\Actions\ExportCsvBulkAction;
use App\Filament\Resources\Invoice\Actions\DownloadPdfAction;
use App\Filament\Resources\Receipt\ReceiptResource;
use App\Helpers\QrCodeHelper;
use App\Mail\InvoiceBalanceMail;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class InvoiceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quotation.number')
                    ->label('From Quotation')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('company.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('balance')
                    ->label('Balance')
                    ->money('USD')
                    ->color(fn ($record) => ($record->balance ?? 0) > 0 ? 'warning' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'info',
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
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('recordPayment')
                        ->label('Record Payment')
                        ->icon('heroicon-o-currency-dollar')
                        ->visible(fn ($record) => ($record->balance ?? 0) > 0)
                        ->form([
                            TextInput::make('amount')
                                ->label('Payment Amount')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->default(fn ($record) => $record->balance)
                                ->extraAttributes(function ($record) {
                                    return [
                                        'x-data' => '{}',
                                        'max' => $record->balance ?? 0,
                                    ];
                                }),
                        ])
                        ->action(function (array $data, $record) {
                            $amount = (float) ($data['amount'] ?? 0);
                            $balance = $record->balance ?? 0;

                            if ($amount <= 0 || $amount > $balance) {
                                Notification::make()
                                    ->title('Invalid amount')
                                    ->body("Amount must be between $0.01 and $" . number_format($balance, 2))
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $last = Receipt::withTrashed()
                                ->where('number', 'like', 'RCT-' . date('Y') . '-%')
                                ->orderBy('number', 'desc')
                                ->first();

                            $nextNumber = $last
                                ? (int) substr($last->number, -4) + 1
                                : 1;

                            $isFullPayment = $amount >= $balance;

                            if ($isFullPayment) {
                                $record->load('items');
                                $subtotal = $record->subtotal;
                                $taxRate = $record->tax_rate;
                                $taxAmount = $record->tax_amount;
                                $discount = $record->discount;
                            } else {
                                $subtotal = $amount;
                                $taxRate = 0;
                                $taxAmount = 0;
                                $discount = 0;
                            }

                            $receipt = Receipt::create([
                                'invoice_id' => $record->id,
                                'company_id' => $record->company_id,
                                'user_id' => auth()->id(),
                                'number' => 'RCT-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
                                'date' => now(),
                                'subtotal' => $subtotal,
                                'tax_rate' => $taxRate,
                                'tax_amount' => $taxAmount,
                                'discount' => $discount,
                                'total' => $amount,
                                'status' => 'issued',
                                'notes' => $isFullPayment ? ($record->notes ?? '') : 'Partial payment for ' . $record->number,
                            ]);

                            if ($isFullPayment) {
                                foreach ($record->items as $item) {
                                    ReceiptItem::create([
                                        'receipt_id' => $receipt->id,
                                        'description' => $item->description,
                                        'quantity' => $item->quantity,
                                        'unit_price' => $item->unit_price,
                                        'amount' => $item->amount,
                                    ]);
                                }
                            } else {
                                ReceiptItem::create([
                                    'receipt_id' => $receipt->id,
                                    'description' => 'Partial payment — ' . $record->number,
                                    'quantity' => 1,
                                    'unit_price' => $amount,
                                    'amount' => $amount,
                                ]);
                            }

                            if ($record->fresh()->balance <= 0) {
                                $record->update(['status' => 'paid']);
                            }

                            Transaction::create([
                                'company_id' => $receipt->company_id,
                                'user_id' => $receipt->user_id,
                                'type' => 'receipt',
                                'document_number' => $receipt->number,
                                'document_id' => $receipt->id,
                                'document_type' => Receipt::class,
                                'amount' => $receipt->total,
                                'date' => $receipt->date,
                                'status' => $receipt->status,
                                'description' => 'Receipt ' . $receipt->number,
                            ]);

                            Notification::make()
                                ->title('Payment recorded')
                                ->body('Receipt ' . $receipt->number . ' for $' . number_format($amount, 2) . ' created.')
                                ->success()
                                ->send();
                        }),
                    Action::make('emailInvoice')
                        ->label('Email Invoice')
                        ->icon('heroicon-o-envelope')
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

                            Mail::to($company->email)->send(new InvoiceBalanceMail($record));

                            Notification::make()
                                ->title('Email sent')
                                ->body('Invoice details sent to ' . $company->email)
                                ->success()
                                ->send();
                        }),
                    DownloadPdfAction::make(),
                    Action::make('createFullReceipt')
                        ->label('Create Full Receipt')
                        ->icon('heroicon-o-receipt-percent')
                        ->visible(fn ($record) => $record->status === 'paid' && ($record->balance ?? 0) <= 0)
                        ->action(function ($record) {
                            $last = Receipt::withTrashed()
                                ->where('number', 'like', 'RCT-' . date('Y') . '-%')
                                ->orderBy('number', 'desc')
                                ->first();

                            $nextNumber = $last
                                ? (int) substr($last->number, -4) + 1
                                : 1;

                            $receipt = Receipt::create([
                                'invoice_id' => $record->id,
                                'company_id' => $record->company_id,
                                'user_id' => auth()->id(),
                                'number' => 'RCT-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
                                'date' => now(),
                                'subtotal' => $record->subtotal,
                                'tax_rate' => $record->tax_rate,
                                'tax_amount' => $record->tax_amount,
                                'discount' => $record->discount,
                                'total' => $record->total,
                                'status' => 'issued',
                                'notes' => $record->notes,
                            ]);

                            $record->load('items');

                            foreach ($record->items as $item) {
                                ReceiptItem::create([
                                    'receipt_id' => $receipt->id,
                                    'description' => $item->description,
                                    'quantity' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'amount' => $item->amount,
                                ]);
                            }

                            Transaction::create([
                                'company_id' => $receipt->company_id,
                                'user_id' => $receipt->user_id,
                                'type' => 'receipt',
                                'document_number' => $receipt->number,
                                'document_id' => $receipt->id,
                                'document_type' => Receipt::class,
                                'amount' => $receipt->total,
                                'date' => $receipt->date,
                                'status' => $receipt->status,
                                'description' => 'Receipt ' . $receipt->number,
                            ]);

                            return redirect(ReceiptResource::getUrl('edit', ['record' => $receipt]));
                        }),
                    EditAction::make(),
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->slideOver()
                        ->modalContent(fn ($record) => view('filament.resources.invoice.view-invoice', [
                            'invoice' => $record,
                            'company' => $record?->company,
                            'items' => $record?->items ?? collect(),
                            'qrSvg' => QrCodeHelper::generateSvg($record->public_url),
                        ]))
                        ->modalWidth('3xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportCsvBulkAction::make('exportCsv')
                        ->label('Export CSV')
                        ->columns([
                            'number' => 'Number',
                            'company.name' => 'Company',
                            'date' => 'Date',
                            'due_date' => 'Due Date',
                            'subtotal' => 'Subtotal',
                            'tax_amount' => 'Tax',
                            'discount' => 'Discount',
                            'total' => 'Total',
                            'paid_amount' => 'Paid',
                            'status' => 'Status',
                        ])
                        ->fileName('invoices-export.csv'),
                    \App\Filament\Actions\ExportPdfBulkAction::make('exportPdf')
                        ->label('Export PDF')
                        ->pdfView('pdf.bulk-invoices')
                        ->fileName('invoices-export.pdf'),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
