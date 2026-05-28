<?php

namespace App\Filament\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;

class ExportPdfBulkAction extends BulkAction
{
    protected string | Closure | null $pdfView = null;

    protected Closure | null $pdfViewData = null;

    protected string | Closure $fileName = 'export.pdf';

    public static function make(?string $name = 'exportPdf'): static
    {
        return parent::make($name)
            ->icon('heroicon-o-document-arrow-down')
            ->action(function (Collection $records, self $self) {
                $filename = $self->getFileName();
                $view = $self->getPdfView();
                $data = $self->evaluate($self->pdfViewData, ['records' => $records]) ?? ['records' => $records];

                $pdf = Pdf::loadView($view, $data);

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, $filename, ['Content-Type' => 'application/pdf']);
            });
    }

    public function pdfView(string | Closure $view): static
    {
        $this->pdfView = $view;
        return $this;
    }

    public function getPdfView(): ?string
    {
        return $this->evaluate($this->pdfView);
    }

    public function pdfViewData(Closure $callback): static
    {
        $this->pdfViewData = $callback;
        return $this;
    }

    public function fileName(string | Closure $name): static
    {
        $this->fileName = $name;
        return $this;
    }

    public function getFileName(): string
    {
        return $this->evaluate($this->fileName);
    }
}
