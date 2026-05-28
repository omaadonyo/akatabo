<?php

namespace App\Filament\Actions;

use Closure;
use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportCsvBulkAction extends BulkAction
{
    protected array | Closure $columns = [];

    protected string | Closure $fileName = 'export.csv';

    protected Closure | null $formatRow = null;

    public static function make(?string $name = 'exportCsv'): static
    {
        return parent::make($name)
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function (Collection $records, self $action) {
                $columns = $action->getColumns();
                $headers = array_values($columns);
                $keys = array_keys($columns);
                $filename = $action->getFileName();

                return response()->streamDownload(function () use ($records, $keys, $columns, $action) {
                    $handle = fopen('php://output', 'w');
                    fwrite($handle, "\xEF\xBB\xBF");
                    fputcsv($handle, array_values($columns));

                    foreach ($records as $record) {
                        $row = [];
                        foreach ($keys as $key) {
                            $row[] = $record->$key ?? '';
                        }
                        if ($action->formatRow) {
                            $row = ($action->formatRow)($record, $row);
                        }
                        fputcsv($handle, $row);
                    }

                    fclose($handle);
                }, $filename, ['Content-Type' => 'text/csv']);
            });
    }

    public function columns(array | Closure $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->evaluate($this->columns);
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

    public function formatRow(Closure $callback): static
    {
        $this->formatRow = $callback;
        return $this;
    }
}
