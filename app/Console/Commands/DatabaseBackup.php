<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--email= : Email to send backup to}';

    protected $description = 'Backup the MySQL database and email to admin';

    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $db = config('database.connections.mysql');
        $mysqldump = env('DB_DUMP_PATH', '"C:\xampp\mysql\bin\mysqldump.exe"');
        $command = sprintf(
            '%s --host=%s --port=%s --user=%s --password=%s %s --routines --triggers --single-transaction > "%s" 2>&1',
            $mysqldump,
            escapeshellarg($db['host']),
            escapeshellarg($db['port']),
            escapeshellarg($db['username']),
            escapeshellarg($db['password'] ?? ''),
            escapeshellarg($db['database']),
            $path
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            Log::error('Database backup failed: ' . implode("\n", $output));
            $this->error('Backup failed: ' . implode("\n", $output));
            return 1;
        }

        $this->info('Backup created: ' . $filename);
        Log::info('Database backup created: ' . $filename);

        $email = $this->option('email');
        if (!$email) {
            $adminUser = \App\Models\User::where('role', 'admin')->first();
            if ($adminUser) {
                $email = $adminUser->email;
            }
        }

        if ($email) {
            try {
                Mail::raw(
                    'Database backup for ' . config('app.name') . ' - ' . now()->format('Y-m-d H:i:s'),
                    function ($message) use ($email, $path, $filename) {
                        $message->to($email)
                            ->subject(config('app.name') . ' Database Backup - ' . now()->format('Y-m-d'))
                            ->attach($path, ['as' => $filename, 'mime' => 'application/sql']);
                    }
                );
                $this->info('Backup emailed to: ' . $email);
                Log::info('Database backup emailed to: ' . $email);
            } catch (\Exception $e) {
                Log::error('Failed to email backup: ' . $e->getMessage());
                $this->warn('Backup created but email failed: ' . $e->getMessage());
            }
        }

        $this->cleanupOldBackups();

        return 0;
    }

    protected function cleanupOldBackups()
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) return;

        $cutoff = now()->subDays(7);
        foreach (glob($backupDir . '/backup-*.sql') as $file) {
            $fileTime = \Carbon\Carbon::createFromTimestamp(filectime($file));
            if ($fileTime->lt($cutoff)) {
                unlink($file);
                $this->info('Deleted old backup: ' . basename($file));
            }
        }
    }
}
