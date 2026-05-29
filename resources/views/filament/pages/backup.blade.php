<div style="padding: 24px; font-family: system-ui, sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 600; color: #1f2937; margin: 0;">Database Backup</h1>
            @if($lastBackup = $this->getLastBackupAttribute())
                <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">
                    Last backup: {{ $lastBackup }}
                </p>
            @else
                <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">
                    No backups yet
                </p>
            @endif
        </div>
        <div>
            {{ $this->runBackupAction }}
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Filename</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Size (MB)</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Date</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getBackupFiles() as $file)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px 16px; font-size: 14px; color: #374151;">{{ $file['name'] }}</td>
                        <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">{{ $file['size'] }}</td>
                        <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">{{ $file['date'] }}</td>
                        <td style="padding: 12px 16px; text-align: right;">
                            <a href="{{ route('backups.download', basename($file['path'])) }}"
                               style="display: inline-block; padding: 6px 12px; background: #d97706; color: #fff; border-radius: 4px; text-decoration: none; font-size: 13px;">
                                Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 24px 16px; text-align: center; color: #9ca3af; font-size: 14px;">
                            No backup files found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
