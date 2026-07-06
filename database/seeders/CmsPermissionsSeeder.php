<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsCategory;
use App\Models\CmsRole;
use App\Models\CmsRolePermission;

class CmsPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Create CMS Categories (Modules & Resources)
        $dashboard = CmsCategory::firstOrCreate(['slug' => 'dashboard'], ['name' => 'Dashboard', 'type' => 'module', 'sort_order' => 1]);
        $content = CmsCategory::firstOrCreate(['slug' => 'content'], ['name' => 'Content Management', 'type' => 'module', 'sort_order' => 2]);
        $reports = CmsCategory::firstOrCreate(['slug' => 'reports'], ['name' => 'Reports', 'type' => 'module', 'sort_order' => 3]);
        $settings = CmsCategory::firstOrCreate(['slug' => 'settings'], ['name' => 'System Settings', 'type' => 'module', 'sort_order' => 4]);
        
        $dbTracker = CmsCategory::firstOrCreate(['slug' => 'db-tracker'], ['name' => 'Database Tracker', 'type' => 'module', 'sort_order' => 5]);
        CmsCategory::firstOrCreate(['slug' => 'db-tracker-schema', 'parent_id' => $dbTracker->id], ['name' => 'Schema Details', 'type' => 'resource', 'sort_order' => 1]);
        CmsCategory::firstOrCreate(['slug' => 'db-tracker-logs', 'parent_id' => $dbTracker->id], ['name' => 'Query Logs', 'type' => 'resource', 'sort_order' => 2]);
        CmsCategory::firstOrCreate(['slug' => 'db-tracker-backups', 'parent_id' => $dbTracker->id], ['name' => 'Backups', 'type' => 'resource', 'sort_order' => 3]);

        // Children of Content Management
        CmsCategory::firstOrCreate(['slug' => 'docs', 'parent_id' => $content->id], ['name' => 'Docs', 'type' => 'content_type', 'sort_order' => 1]);
        CmsCategory::firstOrCreate(['slug' => 'campaigns', 'parent_id' => $content->id], ['name' => 'Campaigns', 'type' => 'content_type', 'sort_order' => 2]);
        
        // 2. Create Roles
        $admin = CmsRole::firstOrCreate(['id' => 'administrator'], ['name' => 'Administrator', 'description' => 'Full system access', 'is_system' => true]);
        $editor = CmsRole::firstOrCreate(['id' => 'content_editor'], ['name' => 'Content Editor', 'description' => 'Manage all content']);
        $viewer = CmsRole::firstOrCreate(['id' => 'report_viewer'], ['name' => 'Report Viewer', 'description' => 'Read-only access to analytics']);

        // 3. Assign Permissions
        $actions = ['view', 'create', 'edit', 'delete', 'publish'];
        $categories = CmsCategory::all();

        foreach ($categories as $cat) {
            // Admin gets everything
            foreach ($actions as $act) {
                CmsRolePermission::firstOrCreate(['role_id' => $admin->id, 'category_id' => $cat->id, 'action' => $act], ['is_allowed' => true]);
            }
            
            // Editor gets Content Management permissions (except delete)
            if ($cat->parent_id == $content->id || $cat->id == $content->id) {
                foreach (['view', 'create', 'edit', 'publish'] as $act) {
                    CmsRolePermission::firstOrCreate(['role_id' => $editor->id, 'category_id' => $cat->id, 'action' => $act], ['is_allowed' => true]);
                }
            }
            
            // Viewer gets Reports view
            if ($cat->id == $reports->id || $cat->id == $dashboard->id) {
                CmsRolePermission::firstOrCreate(['role_id' => $viewer->id, 'category_id' => $cat->id, 'action' => 'view'], ['is_allowed' => true]);
            }
        }
    }
}
