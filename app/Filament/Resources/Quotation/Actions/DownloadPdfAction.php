<?php

namespace App\Filament\Resources\Quotation\Actions;

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
                $record->load(['items', 'company']);
                $pdf = Pdf::loadView('pdf.quotation', [
                    'quotation' => $record,
                ]);
                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, "quotation-{$record->number}.pdf");
            });
    }
}
