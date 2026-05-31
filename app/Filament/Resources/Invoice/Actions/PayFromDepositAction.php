<?php

namespace App\Filament\Resources\Invoice\Actions;

use App\Models\CustomerDeposit;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class PayFromDepositAction extends Action
{
    public static function make(?string $name = 'payFromDeposit'): static
    {
        return parent::make($name)
            ->label('Pay from Deposit')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->visible(fn ($record) => ($record->balance ?? 0) > 0 && ($record->customer?->deposit_balance ?? 0) > 0)
            ->modalHeading('Pay from Deposit')
            ->modalDescription(fn ($record) => 'Customer deposit balance: UGX ' . number_format($record->customer?->deposit_balance ?? 0, 2))
            ->form(function ($record) {
                $maxAmount = min($record->balance ?? 0, $record->customer?->deposit_balance ?? 0);
                return [
                    TextInput::make('amount')
                        ->label('Payment Amount')
                        ->numeric()
                        ->prefix('UGX')
                        ->required()
                        ->default($maxAmount)
                        ->extraAttributes([
                            'max' => $maxAmount,
                        ]),
                ];
            })
            ->action(function (array $data, $record) {
                $amount = (float) ($data['amount'] ?? 0);
                $balance = $record->balance ?? 0;
                $depositBalance = $record->customer?->deposit_balance ?? 0;
                $maxAmount = min($balance, $depositBalance);

                if ($amount <= 0 || $amount > $maxAmount) {
                    Notification::make()
                        ->title('Invalid amount')
                        ->body("Amount must be between 0.01 and UGX " . number_format($maxAmount, 2))
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
                    'customer_id' => $record->customer_id,
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

                CustomerDeposit::create([
                    'company_id' => $record->company_id,
                    'customer_id' => $record->customer_id,
                    'amount' => $amount,
                    'type' => 'payment',
                    'reference_type' => 'invoice',
                    'reference_id' => $record->id,
                    'notes' => 'Payment for invoice ' . $record->number,
                    'date' => now(),
                ]);

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
                    'description' => 'Receipt ' . $receipt->number . ' (paid from deposit)',
                ]);

                Notification::make()
                    ->title('Payment recorded')
                    ->body('UGX ' . number_format($amount, 2) . ' paid from deposit. Receipt ' . $receipt->number . ' created.')
                    ->success()
                    ->send();
            });
    }
}
