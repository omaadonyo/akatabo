<?php

namespace App\Notifications;

use App\Filament\Resources\Quotation\QuotationResource;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuotationCreatedNotification extends Notification
{
    use Queueable;

    public Quotation $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Quotation Created',
            'body' => 'Quotation ' . $this->quotation->number . ' for ' . ($this->quotation->company->name ?? 'N/A') . ' has been created.',
            'url' => QuotationResource::getUrl('edit', ['record' => $this->quotation]),
            'icon' => 'heroicon-o-document-text',
            'document_number' => $this->quotation->number,
            'total' => $this->quotation->total,
        ];
    }
}
