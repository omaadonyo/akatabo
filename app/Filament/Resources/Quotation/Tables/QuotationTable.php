<?php

namespace App\Filament\Resources\Quotation\Tables;

use App\Filament\Resources\Invoice\InvoiceResource;
use App\Filament\Resources\Quotation\Actions\DownloadPdfAction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class QuotationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
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
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    DownloadPdfAction::make(),
                    Action::make('createInvoice')
                        ->label('Create Invoice')
                        ->icon('heroicon-o-currency-dollar')
                        ->visible(fn ($record) => $record->status === 'accepted')
                        ->action(function ($record) {
                            $last = Invoice::withTrashed()
                                ->where('number', 'like', 'INV-' . date('Y') . '-%')
                                ->orderBy('number', 'desc')
                                ->first();

                            $nextNumber = $last
                                ? (int) substr($last->number, -4) + 1
                                : 1;

                            $invoice = Invoice::create([
                                'quotation_id' => $record->id,
                                'company_id' => $record->company_id,
                                'user_id' => auth()->id(),
                                'number' => 'INV-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
                                'date' => now(),
                                'due_date' => now()->addDays(30),
                                'subtotal' => $record->subtotal,
                                'tax_rate' => $record->tax_rate,
                                'tax_amount' => $record->tax_amount,
                                'discount' => $record->discount,
                                'total' => $record->total,
                                'status' => 'draft',
                                'notes' => $record->notes,
                            ]);

                            $record->load('items');

                            foreach ($record->items as $item) {
                                InvoiceItem::create([
                                    'invoice_id' => $invoice->id,
                                    'description' => $item->description,
                                    'quantity' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'amount' => $item->amount,
                                ]);
                            }

                            return redirect(InvoiceResource::getUrl('edit', ['record' => $invoice]));
                        }),
                    EditAction::make(),
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->slideOver()
                        ->modalContent(fn ($record) => view('filament.resources.quotation.view-quotation', [
                            'quotation' => $record,
                            'company' => $record?->company,
                            'items' => $record?->items ?? collect(),
                        ]))
                        ->modalWidth('3xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
