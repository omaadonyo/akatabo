<?php

namespace App\Mail;

use App\Helpers\QrCodeHelper;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Receipt $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Receipt ' . $this->receipt->number . ' - Payment Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
        );
    }

    public function attachments(): array
    {
        $this->receipt->load(['items', 'company']);
        $qrPath = QrCodeHelper::generatePngFile($this->receipt->public_url);

        $pdf = Pdf::loadView('pdf.receipt', [
            'receipt' => $this->receipt,
            'qrPath' => $qrPath,
        ]);

        return [
            Attachment::fromData(function () use ($pdf, $qrPath) {
                $output = $pdf->output();
                if (file_exists($qrPath)) { unlink($qrPath); }
                return $output;
            }, "receipt-{$this->receipt->number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
