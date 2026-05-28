<?php

namespace App\Mail;

use App\Helpers\QrCodeHelper;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceBalanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice ' . $this->invoice->number . ' - Balance Due',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-balance',
        );
    }

    public function attachments(): array
    {
        $this->invoice->load(['items', 'company', 'customer']);
        $qrPath = QrCodeHelper::generatePngFile($this->invoice->public_url);
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice,
            'qrPath' => $qrPath,
        ]);

        return [
            Attachment::fromData(function () use ($pdf, $qrPath) {
                $output = $pdf->output();
                if (file_exists($qrPath)) { unlink($qrPath); }
                return $output;
            }, "invoice-{$this->invoice->number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
