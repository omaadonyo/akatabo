<?php

namespace App\Notifications;

use App\Filament\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification
{
    use Queueable;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Invoice Generated',
            'body' => 'Invoice ' . $this->invoice->number . ' for ' . ($this->invoice->company->name ?? 'N/A') . ' has been generated.',
            'url' => InvoiceResource::getUrl('edit', ['record' => $this->invoice]),
            'icon' => 'heroicon-o-currency-dollar',
            'document_number' => $this->invoice->number,
            'total' => $this->invoice->total,
        ];
    }
}
