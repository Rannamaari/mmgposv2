<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log, Storage};
use Spatie\Backup\Tasks\Backup\BackupJob;
use Spatie\Backup\BackupDestination\BackupDestination;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--notify : Send notification after backup}';
    protected $description = 'Create a database backup';

    public function handle(): int
    {
        $this->info('🔄 Starting MMG POS Database Backup...');

        try {
            // Create backup directory if it doesn't exist
            if (!Storage::disk('backup')->exists('')) {
                Storage::disk('backup')->makeDirectory('');
                $this->info('📁 Created backup directory');
            }

            // Run backup
            $this->info('💾 Creating backup...');
            
            $backupJob = new BackupJob();
            $backupJob->run();

            // Get backup destination to check if backup was successful
            $backupDestination = BackupDestination::create('backup', config('backup.backup.name'));
            $newestBackup = $backupDestination->newestBackup();

            if ($newestBackup) {
                $size = $this->formatBytes($newestBackup->size());
                $this->info("✅ Backup completed successfully!");
                $this->info("📦 File: {$newestBackup->path()}");
                $this->info("📏 Size: {$size}");

                Log::channel('pos')->info('Database backup completed', [
                    'backup_path' => $newestBackup->path(),
                    'backup_size' => $newestBackup->size(),
                    'backup_date' => $newestBackup->date(),
                ]);

                if ($this->option('notify')) {
                    $this->notifySuccess($newestBackup);
                }

                return 0;
            } else {
                $this->error('❌ Backup failed - no backup file created');
                Log::channel('security')->error('Database backup failed - no backup file created');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: {$e->getMessage()}");
            Log::channel('security')->error('Database backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->option('notify')) {
                $this->notifyFailure($e);
            }

            return 1;
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function notifySuccess($backup): void
    {
        $size = $this->formatBytes($backup->size());
        
        Log::channel('pos')->info('Backup notification sent', [
            'type' => 'success',
            'backup_path' => $backup->path(),
            'backup_size' => $backup->size(),
        ]);
    }

    private function notifyFailure(\Exception $e): void
    {
        Log::channel('security')->critical('Backup failure notification sent', [
            'error' => $e->getMessage(),
            'timestamp' => now(),
        ]);
    }
}