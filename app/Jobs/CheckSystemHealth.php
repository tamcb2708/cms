<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CoreConfig;
use PDO;

class CheckSystemHealth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check DB Tracker connections
        $host = CoreConfig::getValue('database/tracker/host', 'default', 0);
        $port = CoreConfig::getValue('database/tracker/port', 'default', 0);
        $dbname = CoreConfig::getValue('database/tracker/dbname', 'default', 0);
        $user = CoreConfig::getValue('database/tracker/username', 'default', 0);
        $password = CoreConfig::getValue('database/tracker/password', 'default', 0);

        if (!$host || !$dbname || !$user) {
            return;
        }

        try {
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Example check: Cache Hit Ratio
            $stmt = $pdo->query("SELECT CASE WHEN sum(blks_hit + blks_read) > 0 THEN sum(blks_hit) * 100 / sum(blks_hit + blks_read) ELSE 0 END as cache_hit_ratio FROM pg_stat_database WHERE datname = '{$dbname}'");
            $cacheHit = (float) $stmt->fetchColumn();

            // Example check: Active connections count
            $stmt2 = $pdo->query("SELECT COUNT(*) FROM pg_stat_activity WHERE datname = '{$dbname}' AND state = 'active'");
            $activeCount = (int) $stmt2->fetchColumn();

            // Threshold checks (can be configured via CoreConfig later)
            if ($cacheHit < 90 && $cacheHit > 0) {
                // E.g., Log to system_notifications table or send email
                \Log::warning("System Health Alert: DB Cache Hit Ratio is below 90% (Current: {$cacheHit}%). Performance may be degraded.");
            }

            if ($activeCount > 50) {
                \Log::warning("System Health Alert: High number of active DB queries ({$activeCount}). System might be under heavy load.");
            }
            
            // Cập nhật trạng thái kết nối thành công
            CoreConfig::updateOrCreate(
                ['path' => 'database/tracker/status', 'scope' => 'default', 'scope_id' => 0],
                ['value' => 'connected']
            );

        } catch (\Exception $e) {
            \Log::error("System Health Check Failed: Could not connect to DB Tracker instance. Error: " . $e->getMessage());
            
            // Cập nhật trạng thái kết nối thất bại
            CoreConfig::updateOrCreate(
                ['path' => 'database/tracker/status', 'scope' => 'default', 'scope_id' => 0],
                ['value' => 'disconnected']
            );
        }
    }
}
