<?php

namespace App\Services;

use PDO;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Cache;

class DbTrackerService
{
    private ?PDO $pdo = null;
    private array $creds;

    public function __construct(array $creds)
    {
        $this->creds = $creds;
    }

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "pgsql:host={$this->creds['host']};port={$this->creds['port']};dbname={$this->creds['dbname']}";
            $this->pdo = new PDO($dsn, $this->creds['user'], $this->creds['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->pdo;
    }

    public function getTables(): array
    {
        $pdo = $this->connect();
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTableDetails(string $tableName): array
    {
        $cacheKey = "db_tracker_details_" . md5($this->creds['host'] . $this->creds['dbname'] . $tableName);
        
        return Cache::remember($cacheKey, 3600, function () use ($tableName) {
            $pdo = $this->connect();
            
            $stmt = $pdo->prepare("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ?");
            $stmt->execute([$tableName]);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pkStmt = $pdo->prepare("
                SELECT kcu.column_name
                FROM information_schema.table_constraints tco
                JOIN information_schema.key_column_usage kcu ON kcu.constraint_name = tco.constraint_name
                WHERE tco.constraint_type = 'PRIMARY KEY' AND kcu.table_name = ?
            ");
            $pkStmt->execute([$tableName]);
            $pks = $pkStmt->fetchAll(PDO::FETCH_COLUMN);

            $fkStmt = $pdo->prepare("
                SELECT kcu.column_name, ccu.table_name AS foreign_table_name, ccu.column_name AS foreign_column_name 
                FROM information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
                WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name = ?
            ");
            $fkStmt->execute([$tableName]);
            $fks = $fkStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'columns' => $columns,
                'pks' => $pks,
                'fks' => $fks
            ];
        });
    }

    public function getErdMermaid(): string
    {
        $cacheKey = "db_tracker_erd_" . md5($this->creds['host'] . $this->creds['dbname']);
        
        return Cache::remember($cacheKey, 3600, function () {
            $pdo = $this->connect();
            $mermaidStr = "erDiagram\n";
            
            $fkStmt = $pdo->query("
                SELECT tc.table_name, kcu.column_name, ccu.table_name AS foreign_table_name, ccu.column_name AS foreign_column_name 
                FROM information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
                WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = 'public'
            ");
            $relations = $fkStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($relations as $rel) {
                $mermaidStr .= "    {$rel['foreign_table_name']} ||--o{ {$rel['table_name']} : \"{$rel['column_name']}\"\n";
            }
            
            $allTablesStmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            $allTables = array_flip($allTablesStmt->fetchAll(PDO::FETCH_COLUMN));

            $colsStmt = $pdo->query("SELECT table_name, column_name FROM information_schema.columns WHERE table_schema = 'public' AND column_name LIKE '%_id'");
            $possibleFks = $colsStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($possibleFks as $col) {
                $isExplicit = false;
                foreach ($relations as $rel) {
                    if ($rel['table_name'] === $col['table_name'] && $rel['column_name'] === $col['column_name']) {
                        $isExplicit = true; break;
                    }
                }

                if (!$isExplicit) {
                    $baseName = substr($col['column_name'], 0, -3); 
                    $targetTable = $baseName . 's'; 
                    
                    if (!isset($allTables[$targetTable]) && isset($allTables[$baseName])) {
                        $targetTable = $baseName;
                    }

                    if (isset($allTables[$targetTable]) && $targetTable !== $col['table_name']) {
                        $mermaidStr .= "    {$targetTable} ||--o{ {$col['table_name']} : \"{$col['column_name']}\"\n";
                    }
                }
            }
            
            return $mermaidStr;
        });
    }

    public function isInitialized(): bool
    {
        $pdo = $this->connect();
        $stmt = $pdo->query("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'pg_audit_logs')");
        return (bool) $stmt->fetchColumn();
    }

    public function getTrackedTables(): array
    {
        $pdo = $this->connect();
        $tables = [];
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name != 'pg_audit_logs' ORDER BY table_name");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tableName = $row['table_name'];
            $checkTrigger = $pdo->prepare("SELECT 1 FROM pg_trigger WHERE tgname = ?");
            $checkTrigger->execute(["audit_trigger_{$tableName}"]);
            $tables[] = [
                'name' => $tableName,
                'tracked' => (bool) $checkTrigger->fetchColumn()
            ];
        }
        return $tables;
    }

    public function initSystem(): void
    {
        $pdo = $this->connect();
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pg_audit_logs (
                id SERIAL PRIMARY KEY,
                table_name VARCHAR(255) NOT NULL,
                action VARCHAR(50) NOT NULL,
                old_data JSONB,
                new_data JSONB,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE OR REPLACE FUNCTION log_table_changes()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    INSERT INTO pg_audit_logs(table_name, action, new_data) VALUES (TG_TABLE_NAME, TG_OP, row_to_json(NEW));
                    RETURN NEW;
                ELSIF (TG_OP = 'UPDATE') THEN
                    INSERT INTO pg_audit_logs(table_name, action, old_data, new_data) VALUES (TG_TABLE_NAME, TG_OP, row_to_json(OLD), row_to_json(NEW));
                    RETURN NEW;
                ELSIF (TG_OP = 'DELETE') THEN
                    INSERT INTO pg_audit_logs(table_name, action, old_data) VALUES (TG_TABLE_NAME, TG_OP, row_to_json(OLD));
                    RETURN OLD;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    public function toggleTracking(string $table, string $action): void
    {
        $pdo = $this->connect();
        if ($action === 'track') {
            $pdo->exec("
                DROP TRIGGER IF EXISTS audit_trigger_{$table} ON {$table};
                CREATE TRIGGER audit_trigger_{$table}
                AFTER INSERT OR UPDATE OR DELETE ON {$table}
                FOR EACH ROW EXECUTE FUNCTION log_table_changes();
            ");
        } else if ($action === 'untrack') {
            $pdo->exec("DROP TRIGGER IF EXISTS audit_trigger_{$table} ON {$table}");
        }
    }

    public function clearLogs(): void
    {
        $pdo = $this->connect();
        $pdo->exec("TRUNCATE TABLE pg_audit_logs;");
    }

    public function getLogs(int $limit = 50): array
    {
        $pdo = $this->connect();
        $stmt = $pdo->query("SELECT * FROM pg_audit_logs ORDER BY created_at DESC LIMIT {$limit}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveConnections(): array
    {
        $pdo = $this->connect();
        $stmt = $pdo->query("SELECT pid, usename, application_name, client_addr, backend_start, state, query FROM pg_stat_activity WHERE datname = '{$this->creds['dbname']}' AND pid <> pg_backend_pid()");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPerformanceStats(): array
    {
        $pdo = $this->connect();
        $dbname = $this->creds['dbname'];

        // Get states
        $stmt = $pdo->query("SELECT COALESCE(state, 'unknown') as state, COUNT(*) as count FROM pg_stat_activity WHERE datname = '{$dbname}' AND pid <> pg_backend_pid() GROUP BY state");
        $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cache hit ratio
        $stmt2 = $pdo->query("SELECT CASE WHEN sum(blks_hit + blks_read) > 0 THEN sum(blks_hit) * 100 / sum(blks_hit + blks_read) ELSE 0 END as cache_hit_ratio FROM pg_stat_database WHERE datname = '{$dbname}'");
        $cacheHit = round((float) $stmt2->fetchColumn(), 2);
        
        // Activities — Live Connections with IP, app name, duration
        $stmt3 = $pdo->query("
            SELECT
                pid,
                usename,
                COALESCE(client_addr::text, '') AS client_addr,
                client_port,
                COALESCE(application_name, '') AS application_name,
                COALESCE(state, 'unknown') AS state,
                EXTRACT(EPOCH FROM (now() - backend_start))::numeric(10,2) AS duration_sec,
                LEFT(query, 120) AS query
            FROM pg_stat_activity
            WHERE datname = '{$dbname}'
              AND pid <> pg_backend_pid()
            ORDER BY backend_start DESC
            LIMIT 30
        ");
        $activities = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        // Database Stats: Transactions, Deadlocks, Size
        $stmtDb = $pdo->query("SELECT 
            xact_commit, 
            xact_rollback, 
            deadlocks, 
            tup_inserted, 
            tup_updated, 
            tup_deleted,
            pg_size_pretty(pg_database_size('{$dbname}')) as db_size,
            pg_database_size('{$dbname}') as db_size_bytes
            FROM pg_stat_database 
            WHERE datname = '{$dbname}'");
        $dbStats = $stmtDb->fetch(PDO::FETCH_ASSOC) ?: [];

        // Waiting Locks
        $stmtLocks = $pdo->query("SELECT count(*) FROM pg_locks WHERE granted = false");
        $waitingLocks = (int) $stmtLocks->fetchColumn();

        // Long Running Queries
        $stmtSlow = $pdo->query("
            SELECT pid, usename, state, 
                   EXTRACT(EPOCH FROM (now() - query_start))::numeric(10,2) AS running_time_sec,
                   LEFT(query, 200) AS query 
            FROM pg_stat_activity 
            WHERE datname = '{$dbname}' 
              AND state = 'active' 
              AND pid <> pg_backend_pid() 
              AND query_start IS NOT NULL
            ORDER BY running_time_sec DESC 
            LIMIT 5
        ");
        $slowQueries = $stmtSlow->fetchAll(PDO::FETCH_ASSOC);

        return [
            'states' => $states,
            'cacheHit' => $cacheHit,
            'activities' => $activities,
            'dbStats' => $dbStats,
            'waitingLocks' => $waitingLocks,
            'slowQueries' => $slowQueries
        ];
    }

    public function getSecurityStats(): array
    {
        $pdo = $this->connect();
        
        $usersStmt = $pdo->query("SELECT usename as username, usesuper as is_superuser, valuntil as password_expiry FROM pg_user");
        $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

        $rolesStmt = $pdo->query("SELECT rolname as role_name, rolsuper, rolinherit, rolcreaterole, rolcreatedb, rolcanlogin FROM pg_roles");
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

        $privsStmt = $pdo->query("SELECT grantee as user, table_name, privilege_type FROM information_schema.role_table_grants WHERE table_schema = 'public'");
        $privileges = $privsStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'users' => $users,
            'roles' => $roles,
            'privileges' => $privileges
        ];
    }
}
