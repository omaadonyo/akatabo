<div style="padding: 24px; font-family: system-ui, -apple-system, sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); padding: 24px 28px; border: 1px solid #f3f4f6;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #d97706, #f59e0b); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <h1 style="font-size: 22px; font-weight: 700; color: #1f2937; margin: 0;">Database Backup</h1>
            </div>
            @if($lastBackup = $this->getLastBackupAttribute())
                <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 13px;">
                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                        <svg style="width: 14px; height: 14px; color: #22c55e;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Last backup: {{ $lastBackup }}
                    </span>
                </p>
            @else
                <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 13px;">No backups yet</p>
            @endif
        </div>
        <div>
            {{ $this->runBackupAction }}
        </div>
    </div>

    <div style="background: #ffffff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #f3f4f6;">
        <div style="padding: 16px 24px; background: #f9fafb; border-bottom: 1px solid #f3f4f6;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span style="font-size: 13px; font-weight: 600; color: #374151;">Backup Files</span>
                <span style="font-size: 11px; color: #9ca3af;">(older than 7 days are auto-cleaned)</span>
            </div>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <th style="padding: 12px 24px; text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Filename</th>
                    <th style="padding: 12px 24px; text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Size</th>
                    <th style="padding: 12px 24px; text-align: left; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Date</th>
                    <th style="padding: 12px 24px; text-align: right; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getBackupFiles() as $file)
                    <tr style="border-bottom: 1px solid #f9fafb;">
                        <td style="padding: 14px 24px; font-size: 13px; color: #374151; font-weight: 500;">
                            <span style="display: inline-flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px; color: #d97706; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $file['name'] }}
                            </span>
                        </td>
                        <td style="padding: 14px 24px; font-size: 13px; color: #6b7280;">{{ $file['size'] }}</td>
                        <td style="padding: 14px 24px; font-size: 13px; color: #6b7280;">{{ $file['date'] }}</td>
                        <td style="padding: 14px 24px; text-align: right;">
                            <a href="{{ route('backups.download', basename($file['path'])) }}"
                               style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 48px 24px; text-align: center; color: #d1d5db; font-size: 13px;">
                            <div style="margin-bottom: 8px;">
                                <svg style="width: 40px; height: 40px; margin: 0 auto; color: #e5e7eb; display: block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </div>
                            No backup files found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
