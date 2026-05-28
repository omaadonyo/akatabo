<?php

namespace App\Filament\Resources\Quotation\Actions;

use App\Helpers\QrCodeHelper;
use App\Models\Quotation;
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
            ->action(function (Quotation $record) {
                $record->load(['items', 'company', 'customer']);
                $qrPath = QrCodeHelper::generatePngFile($record->public_url);
                $pdf = Pdf::loadView('pdf.quotation', [
                    'quotation' => $record,
                    'qrPath' => $qrPath,
                ]);
                $output = $pdf->output();
                if (file_exists($qrPath)) { unlink($qrPath); }
                return response()->streamDownload(function () use ($output) {
                    echo $output;
                }, "quotation-{$record->number}.pdf");
            });
    }
}
