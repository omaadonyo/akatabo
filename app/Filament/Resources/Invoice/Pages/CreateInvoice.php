<?php

namespace App\Filament\Resources\Invoice\Pages;

use App\Filament\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\InvoiceGeneratedNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (blank($data['number'] ?? null)) {
            $lastInvoice = Invoice::withTrashed()
                ->where('number', 'like', 'INV-' . date('Y') . '-%')
                ->orderBy('number', 'desc')
                ->first();

            if ($lastInvoice) {
                $lastNumber = (int) substr($lastInvoice->number, -4);
                $data['number'] = 'INV-' . date('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $data['number'] = 'INV-' . date('Y') . '-0001';
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $invoice = $this->getRecord();

        Transaction::create([
            'company_id' => $invoice->company_id,
            'user_id' => $invoice->user_id,
            'type' => 'invoice',
            'document_number' => $invoice->number,
            'document_id' => $invoice->id,
            'document_type' => Invoice::class,
            'amount' => $invoice->total,
            'date' => $invoice->date,
            'status' => $invoice->status,
            'description' => 'Invoice ' . $invoice->number,
        ]);

        auth()->user()?->notify(new InvoiceGeneratedNotification($invoice));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
