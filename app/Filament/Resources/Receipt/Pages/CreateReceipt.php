<?php

namespace App\Filament\Resources\Receipt\Pages;

use App\Filament\Resources\Receipt\ReceiptResource;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ReceiptAddedNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateReceipt extends CreateRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['company_id'] = auth()->user()?->currentTenant?->id;

        if (blank($data['number'] ?? null)) {
            $lastReceipt = Receipt::withTrashed()
                ->where('number', 'like', 'RCT-' . date('Y') . '-%')
                ->orderBy('number', 'desc')
                ->first();

            if ($lastReceipt) {
                $lastNumber = (int) substr($lastReceipt->number, -4);
                $data['number'] = 'RCT-' . date('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $data['number'] = 'RCT-' . date('Y') . '-0001';
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $receipt = $this->getRecord();

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

        auth()->user()?->notify(new ReceiptAddedNotification($receipt));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
