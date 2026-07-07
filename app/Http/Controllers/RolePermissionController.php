<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsRole;
use App\Models\CmsCategory;
use App\Models\CmsRolePermission;

class RolePermissionController extends Controller
{
    /**
     * Build the roles/permissions matrix data consumed by the Settings
     * page (Inertia prop) when the "permissions" section is active.
     */
    public function permissionsData(): array
    {
        $hasTables = \Illuminate\Support\Facades\Schema::hasTable('cms_roles') && \Illuminate\Support\Facades\Schema::hasTable('cms_categories');

        $globalActions = ['view', 'create', 'edit', 'delete', 'publish'];
        $categories = collect();
        $rolesData = [];

        if ($hasTables) {
            $roles = CmsRole::with('permissions')->get();

            // Fetch category tree (Modules & Resources) up to 3 levels
            $categoriesTree = CmsCategory::whereNull('parent_id')
                ->with(['children' => function ($q) {
                    $q->orderBy('sort_order')->with(['children' => function ($q2) {
                        $q2->orderBy('sort_order');
                    }]);
                }])
                ->orderBy('sort_order')
                ->get();

            // Flatten the tree for the matrix and compute level dynamically
            $flattenTree = function ($items, $level = 1) use (&$flattenTree, &$categories) {
                foreach ($items as $item) {
                    $item->computed_level = $level;
                    $categories->push($item);
                    if ($item->children && $item->children->isNotEmpty()) {
                        $flattenTree($item->children, $level + 1);
                    }
                }
            };
            $flattenTree($categoriesTree);

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
                    'perms' => (object) $perms,
                ];
            }
        }

        return [
            'rolesData' => $rolesData,
            'categories' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'computed_level' => $c->computed_level,
            ])->values()->all(),
            'globalActions' => $globalActions,
            'hasTables' => $hasTables,
        ];
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
