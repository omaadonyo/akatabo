<?php

namespace App\Notifications;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReceiptAddedNotification extends Notification
{
    use Queueable;

    public Receipt $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Receipt Added',
            'body' => 'Receipt ' . $this->receipt->number . ' for ' . ($this->receipt->company->name ?? 'N/A') . ' has been added.',
            'url' => route('filament.app.resources.receipt.receipts.edit', $this->receipt->id),
            'icon' => 'heroicon-o-receipt-percent',
            'document_number' => $this->receipt->number,
            'total' => $this->receipt->total,
        ];
    }
}
