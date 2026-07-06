<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\CmsCategory;

$dbTracker = CmsCategory::firstOrCreate(
    ['slug' => 'db-tracker'],
    ['name' => 'Database Tracker', 'type' => 'module', 'sort_order' => 5]
);

CmsCategory::firstOrCreate(
    ['slug' => 'db-tracker-schema', 'parent_id' => $dbTracker->id],
    ['name' => 'Schema Details', 'type' => 'resource', 'sort_order' => 1]
);

CmsCategory::firstOrCreate(
    ['slug' => 'db-tracker-logs', 'parent_id' => $dbTracker->id],
    ['name' => 'Query Logs', 'type' => 'resource', 'sort_order' => 2]
);

CmsCategory::firstOrCreate(
    ['slug' => 'db-tracker-backups', 'parent_id' => $dbTracker->id],
    ['name' => 'Backups', 'type' => 'resource', 'sort_order' => 3]
);

echo "DB Tracker categories added successfully.";
