<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Artisan, Log, Storage, DB};

class SystemMaintenance extends Command
{
    protected $signature = 'system:maintenance {--force : Run maintenance without confirmation}';
    protected $description = 'Perform system maintenance tasks';

    public function handle(): int
    {
        $this->info('🔧 MMG POS System Maintenance');

        if (!$this->option('force') && !$this->confirm('This will perform maintenance tasks including cache clearing and log rotation. Continue?')) {
            $this->info('Maintenance cancelled.');
            return 0;
        }

        $tasks = [
            'Cache Optimization' => $this->optimizeCache(),
            'Log Rotation' => $this->rotateLogs(),
            'Database Optimization' => $this->optimizeDatabase(),
            'Storage Cleanup' => $this->cleanupStorage(),
            'Session Cleanup' => $this->cleanupSessions(),
        ];

        foreach ($tasks as $task => $result) {
            if ($result['status'] === 'success') {
                $this->info("✅ {$task}: {$result['message']}");
            } else {
                $this->error("❌ {$task}: {$result['message']}");
            }
        }

        $successCount = count(array_filter($tasks, fn($task) => $task['status'] === 'success'));
        $totalCount = count($tasks);

        $this->info("\n🎯 Maintenance Summary: {$successCount}/{$totalCount} tasks completed successfully");

        Log::channel('pos')->info('System maintenance completed', [
            'tasks_completed' => $successCount,
            'tasks_total' => $totalCount,
            'tasks' => $tasks,
        ]);

        return $successCount === $totalCount ? 0 : 1;
    }

    private function optimizeCache(): array
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            
            // Clear config cache and rebuild
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            
            // Clear route cache and rebuild
            Artisan::call('route:clear');
            Artisan::call('route:cache');
            
            // Clear view cache and rebuild
            Artisan::call('view:clear');
            Artisan::call('view:cache');

            return ['status' => 'success', 'message' => 'Cache optimized'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => "Cache optimization failed: {$e->getMessage()}"];
        }
    }

    private function rotateLogs(): array
    {
        try {
            $logPath = storage_path('logs');
            $rotatedCount = 0;
            $maxSize = 100 * 1024 * 1024; // 100MB

            if (is_dir($logPath)) {
                $logFiles = glob($logPath . '/*.log');
                
                foreach ($logFiles as $logFile) {
                    if (filesize($logFile) > $maxSize) {
                        $timestamp = date('Y-m-d_H-i-s');
                        $rotatedFile = str_replace('.log', "_{$timestamp}.log", $logFile);
                        
                        if (rename($logFile, $rotatedFile)) {
                            // Compress the rotated log
                            if (function_exists('gzopen')) {
                                $gzFile = $rotatedFile . '.gz';
                                $data = file_get_contents($rotatedFile);
                                $gz = gzopen($gzFile, 'w9');
                                gzwrite($gz, $data);
                                gzclose($gz);
                                unlink($rotatedFile);
                            }
                            $rotatedCount++;
                        }
                    }
                }
            }

            return ['status' => 'success', 'message' => "Rotated {$rotatedCount} log files"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => "Log rotation failed: {$e->getMessage()}"];
        }
    }

    private function optimizeDatabase(): array
    {
        try {
            // Get table statistics
            $tableStats = DB::select("
                SELECT schemaname, tablename, n_tup_ins, n_tup_upd, n_tup_del
                FROM pg_stat_user_tables 
                WHERE schemaname = 'public'
            ");

            $optimizedTables = 0;
            
            foreach ($tableStats as $stat) {
                $totalOperations = $stat->n_tup_ins + $stat->n_tup_upd + $stat->n_tup_del;
                
                // Analyze tables with significant activity
                if ($totalOperations > 1000) {
                    DB::statement("ANALYZE {$stat->tablename}");
                    $optimizedTables++;
                }
            }

            return ['status' => 'success', 'message' => "Optimized {$optimizedTables} database tables"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => "Database optimization failed: {$e->getMessage()}"];
        }
    }

    private function cleanupStorage(): array
    {
        try {
            $cleanedSize = 0;
            
            // Clean temporary files
            $tempFiles = glob(storage_path('app/temp/*'));
            foreach ($tempFiles as $file) {
                if (is_file($file) && time() - filemtime($file) > 86400) { // 24 hours
                    $cleanedSize += filesize($file);
                    unlink($file);
                }
            }

            // Clean old backup temp files
            $backupTempPath = storage_path('app/backup-temp');
            if (is_dir($backupTempPath)) {
                $tempFiles = glob($backupTempPath . '/*');
                foreach ($tempFiles as $file) {
                    if (is_file($file) && time() - filemtime($file) > 3600) { // 1 hour
                        $cleanedSize += filesize($file);
                        unlink($file);
                    }
                }
            }

            $cleanedSizeMB = round($cleanedSize / 1024 / 1024, 2);
            return ['status' => 'success', 'message' => "Cleaned {$cleanedSizeMB}MB of temporary files"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => "Storage cleanup failed: {$e->getMessage()}"];
        }
    }

    private function cleanupSessions(): array
    {
        try {
            // Clear expired sessions
            $sessionPath = storage_path('framework/sessions');
            $cleanedCount = 0;

            if (is_dir($sessionPath)) {
                $sessionFiles = glob($sessionPath . '/*');
                foreach ($sessionFiles as $file) {
                    if (is_file($file) && time() - filemtime($file) > 7200) { // 2 hours
                        unlink($file);
                        $cleanedCount++;
                    }
                }
            }

            return ['status' => 'success', 'message' => "Cleaned {$cleanedCount} expired sessions"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => "Session cleanup failed: {$e->getMessage()}"];
        }
    }
}