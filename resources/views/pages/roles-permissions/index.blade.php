@php
    $hasTables = \Illuminate\Support\Facades\Schema::hasTable('cms_roles') && \Illuminate\Support\Facades\Schema::hasTable('cms_categories');
    
    $globalActions = ['view', 'create', 'edit', 'delete', 'publish'];
    $rolesData = [];
    $categories = collect();

    if ($hasTables) {
        $roles = \App\Models\CmsRole::with('permissions')->get();
        
        $categoriesTree = \App\Models\CmsCategory::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->orderBy('sort_order')->with(['children' => function($q2) {
                    $q2->orderBy('sort_order');
                }]);
            }])
            ->orderBy('sort_order')
            ->get();
        
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
    }
@endphp


<div x-data="{ 
    showAddRoleModal: false,
    isEdit: false,
    rolesData: {{ json_encode($rolesData) }},
    form: { id: '', name: '', description: '', perms: {} },
    openAdd() {
        this.isEdit = false;
        this.form = { id: '', name: '', description: '', perms: {} };
        this.showAddRoleModal = true;
    },
    openEdit(roleId) {
        this.isEdit = true;
        const role = this.rolesData.find(r => r.id === roleId);
        // Deep clone to avoid mutating original state before save
        this.form = JSON.parse(JSON.stringify(role));
        if (!this.form.perms) this.form.perms = {};
        this.showAddRoleModal = true;
    }
}" class="w-full">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('messages.system_roles') ?? 'System Roles' }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('messages.system_roles_desc') }}</p>
        </div>
        <button type="button" @click="openAdd()" class="inline-flex items-center gap-2 px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-accent-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('messages.role_add_new') }}
        </button>
    </div>

    <!-- Roles List Container -->
    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        @if(!$hasTables)
            <div class="p-8 text-center text-gray-500">
                {!! __('messages.role_init_seed_msg') !!}
            </div>
        @elseif(count($roles) === 0)
            <div class="p-8 text-center text-gray-500 flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <p>{{ __('messages.role_empty_msg') }}</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800/50 dark:text-gray-300 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-4 font-semibold">{{ __('messages.role_name') }}</th>
                        <th class="px-6 py-4 font-semibold">{{ __('messages.role_id') }}</th>
                        <th class="px-6 py-4 font-semibold">{{ __('messages.role_desc') }}</th>
                        <th class="px-6 py-4 font-semibold text-right">{{ __('messages.role_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 dark:text-gray-200">{{ $role->name }}</span>
                                @if($role->is_system)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">SYSTEM</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">
                            {{ $role->id }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            {{ $role->description ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$role->is_system)
                                    <button type="button" @click="openEdit('{{ $role->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-accent-600 hover:text-accent-700 bg-accent-50 hover:bg-accent-100 dark:bg-accent-500/10 dark:text-accent-400 dark:hover:bg-accent-500/20 rounded-md transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        {{ __('messages.role_edit_rules') }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 dark:bg-gray-800/50 rounded-md cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Full Access
                                    </span>
                                @endif
                                
                                @if(!$role->is_system)
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('messages.role_delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 rounded-md transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ __('messages.role_delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Sliding Popup Drawer (Add/Edit Role) -->
    <div x-show="showAddRoleModal" style="display: none;" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="showAddRoleModal" x-transition.opacity class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="showAddRoleModal = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div x-show="showAddRoleModal"
                         x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto w-screen max-w-[800px]">
                        
                        <form action="{{ route('roles.store') }}" method="POST" class="flex h-full flex-col divide-y divide-gray-200 dark:divide-gray-800 bg-gray-50 dark:bg-gray-900 shadow-xl">
                            @csrf
                            <input type="hidden" name="is_edit" :value="isEdit ? '1' : '0'">
                            <div class="h-0 flex-1 overflow-y-auto custom-scrollbar">
                                <div class="bg-accent-600 dark:bg-gray-800 px-4 py-6 sm:px-6 relative overflow-hidden">
                                    <div class="absolute -right-10 -top-10 opacity-10">
                                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                    </div>
                                    <div class="flex items-center justify-between relative z-10">
                                        <h2 class="text-xl font-bold text-white" id="slide-over-title" x-text="isEdit ? '{{ __('messages.role_edit') }}: ' + form.name : '{{ __('messages.role_create') }}'"></h2>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button type="button" @click="showAddRoleModal = false" class="relative rounded-md text-accent-200 hover:text-white focus:outline-none">
                                                <span class="sr-only">{{ __('messages.close_panel') }}</span>
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2 relative z-10">
                                        <p class="text-sm text-accent-100 dark:text-gray-400" x-text="isEdit ? '{{ __('messages.role_edit_desc') }}' : '{{ __('messages.role_create_desc') }}'"></p>
                                    </div>
                                </div>
                                
                                <div class="flex flex-1 flex-col justify-between">
                                    <div class="divide-y divide-gray-200 dark:divide-gray-800 px-4 sm:px-6">
                                        
                                        <!-- Role Basic Info -->
                                        <div class="space-y-6 pb-6 pt-6 grid grid-cols-2 gap-x-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-300">{{ __('messages.role_id') }} <span class="text-red-500">*</span></label>
                                                <input type="text" name="id" x-model="form.id" :readonly="isEdit" :class="isEdit ? 'bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed' : 'dark:bg-gray-900 dark:text-white'" required class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-accent-600 sm:text-sm" placeholder="Ví dụ: content_manager">
                                                <p x-show="isEdit" class="mt-1 text-[11px] text-gray-400">{{ __('messages.role_id_readonly') }}</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-300">{{ __('messages.role_name') }} <span class="text-red-500">*</span></label>
                                                <input type="text" name="name" x-model="form.name" required class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-accent-600 sm:text-sm dark:bg-gray-900 dark:text-white dark:ring-gray-700" placeholder="Ví dụ: Content Manager">
                                            </div>
                                            
                                            <div class="col-span-2">
                                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-300">{{ __('messages.role_desc') }}</label>
                                                <textarea name="description" x-model="form.description" rows="2" class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-accent-600 sm:text-sm dark:bg-gray-900 dark:text-white dark:ring-gray-700"></textarea>
                                            </div>
                                        </div>

                                        <!-- Select Permissions Grid -->
                                        <div class="pb-8 pt-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    {{ __('messages.role_permissions_matrix') }}
                                                </h3>
                                            </div>
                                            
                                            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-800/80">
                                                        <tr>
                                                            <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3">{{ __('messages.role_resource_category') }}</th>
                                                            @foreach(isset($globalActions) ? $globalActions : [] as $action)
                                                                <th scope="col" class="px-2 py-3 text-center text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l border-gray-200 dark:border-gray-700">
                                                                    {{ __('messages.role_action_' . $action) }}
                                                                </th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-gray-50 dark:bg-gray-900">
                                                        @if(isset($categories))
                                                            @foreach($categories as $category)
                                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                                                    <td class="px-5 py-3 text-sm font-bold text-gray-900 dark:text-gray-200" style="padding-left: {{ $category->computed_level > 1 ? ($category->computed_level * 1.5) + 1 : 1.25 }}rem; {{ $category->computed_level === 1 ? 'background-color: rgba(249, 250, 251, 0.5);' : '' }}">
                                                                        @if($category->computed_level > 1)
                                                                            <span class="text-gray-300 dark:text-gray-600 mr-1 font-normal">↳</span>
                                                                        @endif
                                                                        <span class="{{ $category->computed_level > 1 ? 'font-medium text-gray-600 dark:text-gray-400' : 'font-bold' }}">
                                                                            {{ __('messages.category_' . str_replace('-', '_', $category->slug)) !== 'messages.category_' . str_replace('-', '_', $category->slug) ? __('messages.category_' . str_replace('-', '_', $category->slug)) : $category->name }}
                                                                        </span>
                                                                    </td>
                                                                    @foreach($globalActions as $action)
                                                                        <td class="px-2 py-3.5 text-center border-l border-gray-100 dark:border-gray-800">
                                                                            <label class="inline-flex items-center cursor-pointer w-full h-full justify-center">
                                                                                <input type="checkbox" 
                                                                                       name="permissions[{{ $category->id }}][{{ $action }}]" 
                                                                                       value="1" 
                                                                                       :checked="(form.perms[{{ $category->id }}] || {})['{{ $action }}']"
                                                                                       @change="
                                                                                           if(!form.perms[{{ $category->id }}]) form.perms[{{ $category->id }}] = {};
                                                                                           form.perms[{{ $category->id }}]['{{ $action }}'] = $event.target.checked;
                                                                                       "
                                                                                       class="w-4.5 h-4.5 text-accent-600 bg-gray-100 border-gray-300 rounded focus:ring-accent-500 dark:focus:ring-accent-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 transition-all cursor-pointer hover:scale-110">
                                                                            </label>
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end px-6 py-4 gap-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-800">
                                <button type="button" @click="showAddRoleModal = false" class="rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">{{ __('messages.cancel') }}</button>
                                <button type="submit" class="inline-flex justify-center rounded-lg bg-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-accent-500 focus:ring-2 focus:ring-accent-600 focus:ring-offset-2 transition-colors" x-text="isEdit ? '{{ __('messages.save_changes') }}' : '{{ __('messages.role_create_save') }}'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
