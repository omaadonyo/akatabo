<?php

namespace App\Mail;

use App\Helpers\QrCodeHelper;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Quotation $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quotation ' . $this->quotation->number . ' - Accepted',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation',
        );
    }

    public function attachments(): array
    {
        $this->quotation->load(['items', 'company', 'customer']);
        $qrPath = QrCodeHelper::generatePngFile($this->quotation->public_url);

        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $this->quotation,
            'qrPath' => $qrPath,
        ]);

        return [
            Attachment::fromData(function () use ($pdf, $qrPath) {
                $output = $pdf->output();
                if (file_exists($qrPath)) { unlink($qrPath); }
                return $output;
            }, "quotation-{$this->quotation->number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
