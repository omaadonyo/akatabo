<?php

namespace App\Filament\Resources\Quotation\Pages;

use App\Filament\Resources\Quotation\QuotationResource;
use App\Models\Quotation;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\QuotationCreatedNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $lastQuotation = Quotation::withTrashed()
            ->where('number', 'like', 'QOT-' . date('Y') . '-%')
            ->orderBy('number', 'desc')
            ->first();

        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->number, -4);
            $data['number'] = 'QOT-' . date('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $data['number'] = 'QOT-' . date('Y') . '-0001';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $quotation = $this->getRecord();

        Transaction::create([
            'company_id' => $quotation->company_id,
            'user_id' => $quotation->user_id,
            'type' => 'quotation',
            'document_number' => $quotation->number,
            'document_id' => $quotation->id,
            'document_type' => Quotation::class,
            'amount' => $quotation->total,
            'date' => $quotation->date,
            'status' => $quotation->status,
            'description' => 'Quotation ' . $quotation->number,
        ]);

        auth()->user()?->notify(new QuotationCreatedNotification($quotation));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
