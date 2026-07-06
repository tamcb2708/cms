<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoreConfig;
use App\Services\DbTrackerService;
use Exception;
use PDOException;

class DbTrackerController extends Controller
{
    private function getCreds()
    {
        $enable = CoreConfig::getValue('database/tracker/enable', 'default', 0);
        if ($enable !== '1') {
            return null;
        }

        $host = CoreConfig::getValue('database/tracker/host', 'default', 0);
        $port = CoreConfig::getValue('database/tracker/port', 'default', 0);
        $dbname = CoreConfig::getValue('database/tracker/dbname', 'default', 0);
        $user = CoreConfig::getValue('database/tracker/username', 'default', 0);
        $password = CoreConfig::getValue('database/tracker/password', 'default', 0);

        if (!$host || !$dbname || !$user) {
            return null;
        }

        return [
            'host' => $host,
            'port' => $port ?: 5432,
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password
        ];
    }

    private function getService(): ?DbTrackerService
    {
        $creds = $this->getCreds();
        if (!$creds) {
            return null;
        }
        return new DbTrackerService($creds);
    }

    // Default route redirects to one of the features
    public function index()
    {
        return redirect()->route('db-tracker.data');
    }

    // 1. Schema Information
    public function schema(Request $request)
    {
        $service = $this->getService();
        if (!$service) {
            return view('pages.db-tracker.missing-config');
        }

        try {
            $tables = $service->getTables();
        } catch (PDOException $e) {
            return view('pages.db-tracker.missing-config')->withErrors(['error' => 'Connection failed: ' . $e->getMessage()]);
        }

        $creds = $this->getCreds();
        return view('pages.db-tracker.schema', compact('creds', 'tables'));
    }

    public function schemaTableDetails(Request $request, $tableName)
    {
        $service = $this->getService();
        if (!$service) return response()->json(['success' => false]);
        
        try {
            $data = $service->getTableDetails($tableName);
            return response()->json(array_merge(['success' => true], $data));
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function schemaErd(Request $request)
    {
        $service = $this->getService();
        if (!$service) return response()->json(['success' => false]);
        
        try {
            $mermaid = $service->getErdMermaid();
            return response()->json([
                'success' => true,
                'mermaid' => $mermaid
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 2. Data & Activity (The existing Dashboard)
    public function data(Request $request)
    {
        $service = $this->getService();
        if (!$service) {
            return view('pages.db-tracker.missing-config');
        }

        try {
            $isInitialized = $service->isInitialized();
            $tables = [];
            if ($isInitialized) {
                $tables = $service->getTrackedTables();
            }
        } catch (PDOException $e) {
            return view('pages.db-tracker.missing-config')->withErrors(['error' => 'Connection failed: ' . $e->getMessage()]);
        }

        $creds = $this->getCreds();
        $tab = 1; // Keeping $tab variable for backward compatibility in blade if needed
        return view('pages.db-tracker.dashboard', compact('tab', 'creds', 'isInitialized', 'tables'));
    }

    // 3. Performance & Statistics
    public function performance(Request $request)
    {
        $service = $this->getService();
        if (!$service) {
            return view('pages.db-tracker.missing-config');
        }
        
        try {
            $activities = $service->getActiveConnections();
        } catch (PDOException $e) {
            return view('pages.db-tracker.missing-config')->withErrors(['error' => 'Connection failed: ' . $e->getMessage()]);
        }
        
        $creds = $this->getCreds();
        return view('pages.db-tracker.performance', compact('creds', 'activities'));
    }

    public function performanceStats(Request $request)
    {
        $service = $this->getService();
        if (!$service) return response()->json(['success' => false]);
        
        try {
            $stats = $service->getPerformanceStats();
            return response()->json(array_merge(['success' => true], $stats));
        } catch (PDOException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 4. Security & Users
    public function security(Request $request)
    {
        $service = $this->getService();
        if (!$service) {
            return view('pages.db-tracker.missing-config');
        }
        
        try {
            $stats = $service->getSecurityStats();
            $users = $stats['users'];
            $roles = $stats['roles'];
            $privileges = $stats['privileges'];
        } catch (PDOException $e) {
            return view('pages.db-tracker.missing-config')->withErrors(['error' => 'Connection failed: ' . $e->getMessage()]);
        }
        
        $creds = $this->getCreds();
        return view('pages.db-tracker.security', compact('creds', 'users', 'roles', 'privileges'));
    }

    // 5. Backups Management
    public function backups(Request $request)
    {
        $service = $this->getService();
        if (!$service) {
            return view('pages.db-tracker.missing-config');
        }
        
        $creds = $this->getCreds();
        
        // Mock data for AWS RDS Snapshots & S3 Backups
        $snapshots = [
            ['id' => 'rds-snap-17202350', 'type' => 'Automated', 'size' => '2.4 GB', 'created_at' => now()->subHours(2)->format('Y-m-d H:i:s'), 'status' => 'Available'],
            ['id' => 'rds-snap-17201480', 'type' => 'Automated', 'size' => '2.4 GB', 'created_at' => now()->subDays(1)->format('Y-m-d H:i:s'), 'status' => 'Available'],
            ['id' => 'manual-pre-update-v2', 'type' => 'Manual', 'size' => '2.3 GB', 'created_at' => now()->subDays(3)->format('Y-m-d H:i:s'), 'status' => 'Available'],
        ];

        return view('pages.db-tracker.backups', compact('creds', 'snapshots'));
    }

    // Generic action handler for DB Tracker Dashboard
    public function action(Request $request)
    {
        $service = $this->getService();
        if (!$service) return response()->json(['success' => false]);

        $action = $request->input('action');
        $table = $request->input('table');

        try {
            if ($action === 'init') {
                $service->initSystem();
            } else if (in_array($action, ['track', 'untrack']) && $table) {
                $service->toggleTracking($table, $action);
            } else if ($action === 'clear') {
                $service->clearLogs();
            } else {
                return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.']);
            }
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Fetch logs specifically for DB Tracker Dashboard
    public function logs(Request $request)
    {
        $service = $this->getService();
        if (!$service) {
            return view('pages.partials.db-tracker-logs', ['status' => 'not_connected', 'logs' => []]);
        }
        
        try {
            $logs = $service->getLogs(50);
            return view('pages.partials.db-tracker-logs', ['status' => 'success', 'logs' => $logs]);
        } catch (Exception $e) {
            return view('pages.partials.db-tracker-logs', ['status' => 'not_initialized', 'logs' => []]);
        }
    }
}
