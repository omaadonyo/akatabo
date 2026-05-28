<?php

namespace App\Filament\Resources\Receipt\Actions;

use App\Helpers\QrCodeHelper;
use App\Models\Receipt;
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
            ->action(function (Receipt $record) {
                $record->load(['items', 'company']);
                $qrPath = QrCodeHelper::generatePngFile($record->public_url);
                $pdf = Pdf::loadView('pdf.receipt', [
                    'receipt' => $record,
                    'qrPath' => $qrPath,
                ]);
                $output = $pdf->output();
                if (file_exists($qrPath)) { unlink($qrPath); }
                return response()->streamDownload(function () use ($output) {
                    echo $output;
                }, "receipt-{$record->number}.pdf");
            });
    }
}
