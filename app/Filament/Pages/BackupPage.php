<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class BackupPage extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Database Backup';

    protected string $view = 'filament.pages.backup';

    protected static ?int $navigationSort = 1;

    public function getBackupFiles(): array
    {
        $files = [];
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) return $files;

        foreach (glob($backupDir . '/backup-*.sql') as $path) {
            $files[] = [
                'name' => basename($path),
                'size' => round(filesize($path) / 1024 / 1024, 2),
                'date' => date('Y-m-d H:i:s', filectime($path)),
                'path' => $path,
            ];
        }

        rsort($files);

        return $files;
    }

    public function getLastBackupAttribute(): ?string
    {
        $files = $this->getBackupFiles();
        return $files[0]['date'] ?? null;
    }

    public function runBackupAction(): Action
    {
        return Action::make('runBackup')
            ->label('Run Backup Now')
            ->icon('heroicon-o-play')
            ->color('primary')
            ->action(function () {
                $exitCode = Artisan::call('db:backup');
                $output = Artisan::output();

                if ($exitCode === 0) {
                    Notification::make()
                        ->title('Backup completed successfully')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Backup failed')
                        ->body($output)
                        ->danger()
                        ->send();
                }
            });
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
