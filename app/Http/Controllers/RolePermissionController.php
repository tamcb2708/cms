<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsRole;
use App\Models\CmsCategory;
use App\Models\CmsRolePermission;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Fetch all roles
        $roles = CmsRole::with('permissions')->get();

        // Fetch category tree (Modules & Resources) up to 3 levels
        $categoriesTree = CmsCategory::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->orderBy('sort_order')->with(['children' => function($q2) {
                    $q2->orderBy('sort_order');
                }]);
            }])
            ->orderBy('sort_order')
            ->get();

        // Flatten the tree for the matrix and compute level dynamically
        $categories = collect();
        $flattenTree = function($items, $level = 1) use (&$flattenTree, &$categories) {
            foreach ($items as $item) {
                $item->computed_level = $level;
                $categories->push($item);
                if ($item->children && $item->children->isNotEmpty()) {
                    $flattenTree($item->children, $level + 1);
                }
            }
        };
        $flattenTree($categoriesTree);

        // Pre-defined possible actions for any category (in a real app, this could be stored in cms_category_settings)
        $globalActions = ['view', 'create', 'edit', 'delete', 'publish'];

        // Build a structured array for the Matrix View
        // $matrix[category_id][role_id][action] = true/false
        $permissionsMap = [];
        foreach ($roles as $role) {
            foreach ($role->permissions as $perm) {
                $permissionsMap[$perm->category_id][$role->id][$perm->action] = $perm->is_allowed;
            }
        }

        $hasTables = \Illuminate\Support\Facades\Schema::hasTable('cms_roles') && \Illuminate\Support\Facades\Schema::hasTable('cms_categories');

        $rolesData = [];
        foreach ($roles as $role) {
            $perms = [];
            foreach ($role->permissions as $perm) {
                if ($perm->is_allowed) {
                    $perms[$perm->category_id][$perm->action] = true;
                }
            }
            $rolesData[] = [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'perms' => (object)$perms
            ];
        }

        return view('pages.roles-permissions.index', compact('roles', 'categories', 'globalActions', 'permissionsMap', 'rolesData', 'hasTables'));
    }

    public function store(Request $request)
    {
        $isEdit = $request->input('is_edit') === '1';

        $rules = [
            'id' => 'required|string',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array'
        ];

        if (!$isEdit) {
            $rules['id'] .= '|unique:cms_roles,id';
        }

        $request->validate($rules);

        if ($isEdit) {
            $existingRole = CmsRole::find($request->id);
            if ($existingRole && $existingRole->is_system) {
                return back()->with('error', __('messages.role_system_cannot_edit'));
            }
        }

        $role = CmsRole::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'description' => $request->description,
            ]
        );

        // Process permissions from popup form
        // First delete all existing permissions for this role
        CmsRolePermission::where('role_id', $role->id)->delete();

        // Then re-insert the selected ones
        if ($request->has('permissions')) {
            $inserts = [];
            $now = now();
            foreach ($request->permissions as $categoryId => $actions) {
                foreach ($actions as $action => $value) {
                    if ($value) {
                        $inserts[] = [
                            'role_id' => $role->id,
                            'category_id' => $categoryId,
                            'action' => $action,
                            'is_allowed' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
            if (!empty($inserts)) {
                CmsRolePermission::insert($inserts);
            }
        }

        $msg = $isEdit ? __('messages.role_update_success') : __('messages.role_create_success');
        return back()->with('success', $msg);
    }

    public function updatePermissions(Request $request)
    {
        // Future endpoint for bulk updating
        return back()->with('success', 'Permissions updated successfully.');
    }

    public function destroy($id)
    {
        $role = CmsRole::findOrFail($id);

        if ($role->is_system) {
            return back()->with('error', __('messages.role_delete_system_error'));
        }

        $role->delete();

        return back()->with('success', __('messages.role_delete_success'));
    }
}
