<?php

namespace App\Filament\Resources\Invoice\Actions;

use App\Helpers\QrCodeHelper;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;

class DownloadPdfAction extends Action
{
    public static function make(?string $name = 'downloadPdf'): static
    {
        return parent::make($name)
            ->label('Download PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function (Invoice $record) {
                $record->load(['items', 'company', 'customer']);
                $qrPath = QrCodeHelper::generatePngFile($record->public_url);
                $pdf = Pdf::loadView('pdf.invoice', [
                    'invoice' => $record,
                    'qrPath' => $qrPath,
                ]);
                $output = $pdf->output();
                if (file_exists($qrPath)) { unlink($qrPath); }
                return response()->streamDownload(function () use ($output) {
                    echo $output;
                }, "invoice-{$record->number}.pdf");
            });
    }
}
