<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CoreConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PDO;
use Exception;

class WarmUpSystemCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user = null)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('System cache warming started...');

        // 1. Warm up CoreConfig settings for global/default scope
        $paths = [
            'database/tracker/host',
            'database/tracker/port',
            'database/tracker/dbname',
            'database/tracker/username',
            'database/tracker/password',
            'general/country/default',
            'general/country/allow',
            'general/store_information/name',
            'design/theme/color',
            'security/session/timeout'
        ];

        foreach ($paths as $path) {
            CoreConfig::getValue($path, 'default', 0);
        }
        
        Log::info('CoreConfig keys warmed up.');

        // 2. Pre-connect to DB Tracker to warm up DNS, TCP, and Postgres catalog
        $host = CoreConfig::getValue('database/tracker/host', 'default', 0);
        $port = CoreConfig::getValue('database/tracker/port', 'default', 0) ?: 5432;
        $dbname = CoreConfig::getValue('database/tracker/dbname', 'default', 0);
        $user = CoreConfig::getValue('database/tracker/username', 'default', 0);
        $password = CoreConfig::getValue('database/tracker/password', 'default', 0);

        if ($host && $dbname && $user) {
            try {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                $pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3 // short timeout for warmup
                ]);
                
                // Fetch basic schema to cache it on the PostgreSQL server side
                $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' LIMIT 1");
                $stmt->fetchAll();
                
                Log::info("DB Tracker connection successfully warmed up for database: {$dbname}.");
            } catch (Exception $e) {
                Log::warning("DB Tracker warmup connection failed: " . $e->getMessage());
            }
        }

        Log::info('System cache warming completed.');
    }
}
