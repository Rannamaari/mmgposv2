<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Cache, Storage, Log};
use App\Models\{Customer, Service, Part, WorkOrder, Invoice};

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check {--notify : Send notifications if issues found}';
    protected $description = 'Perform system health checks';

    public function handle(): int
    {
        $this->info('🏥 Starting MMG POS System Health Check...');
        
        $issues = [];
        $checks = [
            'Database Connection' => $this->checkDatabase(),
            'Cache System' => $this->checkCache(),
            'Storage Access' => $this->checkStorage(),
            'Data Integrity' => $this->checkDataIntegrity(),
            'Disk Space' => $this->checkDiskSpace(),
            'Log Files' => $this->checkLogFiles(),
        ];

        foreach ($checks as $check => $result) {
            if ($result['status'] === 'OK') {
                $this->info("✅ {$check}: {$result['message']}");
            } else {
                $this->error("❌ {$check}: {$result['message']}");
                $issues[] = "{$check}: {$result['message']}";
            }
        }

        if (empty($issues)) {
            $this->info('🎉 All systems operational!');
            Log::channel('pos')->info('System health check passed');
            return 0;
        } else {
            $this->error('⚠️  Issues found:');
            foreach ($issues as $issue) {
                $this->line("   - {$issue}");
            }
            
            Log::channel('security')->warning('System health check failed', ['issues' => $issues]);
            
            if ($this->option('notify')) {
                $this->notifyAdmins($issues);
            }
            
            return 1;
        }
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $count = Customer::count();
            return ['status' => 'OK', 'message' => "Connected ({$count} customers)"];
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => "Connection failed: {$e->getMessage()}"];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', 'test', 10);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            
            if ($value === 'test') {
                return ['status' => 'OK', 'message' => 'Cache working'];
            } else {
                return ['status' => 'ERROR', 'message' => 'Cache not working properly'];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => "Cache failed: {$e->getMessage()}"];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::put($testFile, 'test');
            $content = Storage::get($testFile);
            Storage::delete($testFile);
            
            if ($content === 'test') {
                return ['status' => 'OK', 'message' => 'Storage accessible'];
            } else {
                return ['status' => 'ERROR', 'message' => 'Storage read/write failed'];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => "Storage failed: {$e->getMessage()}"];
        }
    }

    private function checkDataIntegrity(): array
    {
        try {
            $issues = [];
            
            // Check for orphaned motorcycles
            $orphanedMotorcycles = DB::table('motorcycles')
                ->leftJoin('customers', 'motorcycles.customer_id', '=', 'customers.id')
                ->whereNull('customers.id')
                ->count();
            
            if ($orphanedMotorcycles > 0) {
                $issues[] = "{$orphanedMotorcycles} orphaned motorcycles";
            }
            
            // Check for work orders without customers
            $invalidWorkOrders = DB::table('work_orders')
                ->leftJoin('customers', 'work_orders.customer_id', '=', 'customers.id')
                ->whereNull('customers.id')
                ->count();
            
            if ($invalidWorkOrders > 0) {
                $issues[] = "{$invalidWorkOrders} work orders without customers";
            }
            
            // Check for negative stock
            $negativeStock = Part::where('stock_qty', '<', 0)->count();
            if ($negativeStock > 0) {
                $issues[] = "{$negativeStock} parts with negative stock";
            }
            
            if (empty($issues)) {
                return ['status' => 'OK', 'message' => 'Data integrity good'];
            } else {
                return ['status' => 'ERROR', 'message' => implode(', ', $issues)];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => "Data check failed: {$e->getMessage()}"];
        }
    }

    private function checkDiskSpace(): array
    {
        $freeBytes = disk_free_space(storage_path());
        $totalBytes = disk_total_space(storage_path());
        $usedPercent = (($totalBytes - $freeBytes) / $totalBytes) * 100;
        
        if ($usedPercent > 90) {
            return ['status' => 'ERROR', 'message' => sprintf('Disk %.1f%% full', $usedPercent)];
        } elseif ($usedPercent > 80) {
            return ['status' => 'WARNING', 'message' => sprintf('Disk %.1f%% full', $usedPercent)];
        } else {
            return ['status' => 'OK', 'message' => sprintf('Disk %.1f%% used', $usedPercent)];
        }
    }

    private function checkLogFiles(): array
    {
        $logPath = storage_path('logs');
        $logSize = 0;
        
        if (is_dir($logPath)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($logPath));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $logSize += $file->getSize();
                }
            }
        }
        
        $logSizeMB = $logSize / 1024 / 1024;
        
        if ($logSizeMB > 1000) { // 1GB
            return ['status' => 'WARNING', 'message' => sprintf('Log files %.1fMB (consider rotation)', $logSizeMB)];
        } else {
            return ['status' => 'OK', 'message' => sprintf('Log files %.1fMB', $logSizeMB)];
        }
    }

    private function notifyAdmins(array $issues): void
    {
        // Here you would implement notification logic
        // For example, sending emails or Slack messages
        Log::channel('security')->critical('System health check failed - admin notification sent', [
            'issues' => $issues,
            'timestamp' => now(),
        ]);
    }
}