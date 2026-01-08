<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ServerStatsService
{
    /**
     * Get all consolidated server data with visual metadata.
     */
    public function getFullDiagnostic(): array
    {
        $os = PHP_OS_FAMILY;

        return [
            'os'      => $this->getOsInfo($os),
            'cpu'     => $this->getCpuStats($os),
            'memory'  => $this->getMemoryStats($os),
            'storage' => $this->getStorageStats(),
            'database'=> $this->getDatabaseStats(),
            'network' => $this->getNetworkStats(),
        ];
    }

    protected function getOsInfo($os): array
    {
        return [
            'name'     => $os,
            'hostname' => gethostname(),
            'kernel'   => php_uname('r'),
            'icon'     => match($os) {
                'Windows' => 'heroicon-m-window',
                'Darwin'  => 'heroicon-m-command-line',
                default   => 'heroicon-m-beaker',
            },
        ];
    }

    protected function getCpuStats($os): array
    {
        $name = Cache::remember('srv_cpu_name', 86400, function() use ($os) {
            if ($os === 'Windows') return trim(shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_Processor).Name"') ?? 'Windows CPU');
            if ($os === 'Linux') {
                $info = @file_get_contents('/proc/cpuinfo');
                preg_match('/model name\s+:\s+(.*)$/m', $info ?? '', $matches);
                return $matches[1] ?? 'Linux CPU';
            }
            return 'Generic Processor';
        });

        $load = 0;
        if ($os === 'Windows') {
            $load = (int) trim(shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_Processor).LoadPercentage"') ?? 0);
        } else {
            $avg = sys_getloadavg();
            $cores = (int) shell_exec('nproc') ?: 4;
            $load = (int) min(100, ($avg[0] * 100 / $cores));
        }

        return [
            'label'   => 'Processor',
            'name'    => $name,
            'usage'   => $load,
            'classes' => $this->getStatusClasses($load),
        ];
    }

    protected function getMemoryStats($os): array
    {
        $total = 1; $used = 0;
        if ($os === 'Linux' && @file_exists('/proc/meminfo')) {
            $mem = file_get_contents('/proc/meminfo');
            preg_match_all('/(\w+):\s+(\d+)/', $mem, $matches);
            $i = array_combine($matches[1], $matches[2]);
            $total = $i['MemTotal'] / 1024 / 1024;
            $used = ($i['MemTotal'] - ($i['MemAvailable'] ?? $i['MemFree'])) / 1024 / 1024;
        } elseif ($os === 'Windows') {
            $total = (float)shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_PhysicalMemory | Measure-Object -Property Capacity -Sum).Sum / 1GB"');
            $free = (float)shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_OperatingSystem).FreePhysicalMemory / 1MB"');
            $used = $total - $free;
        }

        $pct = round(($used / max($total, 1)) * 100);

        return [
            'label'   => 'Memory',
            'total'   => round($total, 1) . ' GB',
            'used'    => round($used, 1) . ' GB',
            'pct'     => $pct,
            'classes' => $this->getStatusClasses($pct),
        ];
    }

    protected function getStorageStats(): array
    {
        $path = base_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;
        $pct = round(($used / $total) * 100);

        return [
            'label'   => 'Storage',
            'total'   => round($total / 1073741824, 1) . ' GB',
            'used'    => round($used / 1073741824, 1) . ' GB',
            'pct'     => $pct,
            'classes' => $this->getStatusClasses($pct, 85, 95),
        ];
    }

    protected function getDatabaseStats(): array
    {
        $driver = DB::connection()->getDriverName();
        $version = match($driver) {
            'sqlite' => DB::select('select sqlite_version() as ver')[0]->ver,
            default  => DB::select('select version() as ver')[0]->ver,
        };

        return [
            'label'   => 'Database',
            'engine'  => ucfirst($driver),
            'version' => $version,
            'status'  => 'Connected',
            'color'   => 'success',
        ];
    }

    protected function getNetworkStats(): array
    {
        $start = microtime(true);
        $connected = @fsockopen("1.1.1.1", 53, $errno, $errstr, 0.5);
        $latency = $connected ? round((microtime(true) - $start) * 1000) : null;
        if($connected) fclose($connected);

        return [
            'label'   => 'Network',
            'ping'    => $latency ? $latency . 'ms' : 'Timeout',
            'online'  => (bool)$latency,
            'color'   => $latency < 150 ? 'success' : ($latency < 300 ? 'warning' : 'danger'),
        ];
    }

    /**
     * Centralized logic for UI Colors.
     */
    protected function getStatusClasses($value, $warning = 70, $danger = 90): array
    {
        if ($value >= $danger) {
            return ['text' => 'text-danger-500', 'bg' => 'bg-danger-500', 'light' => 'bg-danger-500/10', 'border' => 'border-danger-500/20'];
        }
        if ($value >= $warning) {
            return ['text' => 'text-warning-500', 'bg' => 'bg-warning-500', 'light' => 'bg-warning-500/10', 'border' => 'border-warning-500/20'];
        }
        return ['text' => 'text-success-500', 'bg' => 'bg-success-500', 'light' => 'bg-success-500/10', 'border' => 'border-success-500/20'];
    }
}
