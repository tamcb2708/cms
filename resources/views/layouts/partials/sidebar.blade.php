@php
    $isDbTrackerEnabled = \App\Models\CoreConfig::getValue('database/tracker/enable', 'default', 0) == '1';

    $navGroups = [
        [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'href' => route('dashboard'), 'icon' => 'grid', 'lang_key' => 'dashboard'],
            ['label' => 'Content Management', 'route' => 'content-management', 'href' => route('content-management'), 'icon' => 'folder', 'lang_key' => 'content_management'],
            ['label' => 'Reports', 'route' => 'reports', 'href' => route('reports'), 'icon' => 'bar-chart', 'lang_key' => 'reports'],
            ['label' => 'Email Campaigns', 'route' => 'email-campaigns', 'href' => route('email-campaigns'), 'icon' => 'mail', 'lang_key' => 'email_campaigns'],
            ['label' => 'Workspace Subscription', 'route' => null, 'href' => \App\Filament\Resources\TenantResource::getUrl(), 'icon' => 'credit-card', 'lang_key' => 'workspace_subscription'],
        ],
        [
            array_merge(['label' => 'DB Tracker', 'route' => 'db-tracker.*', 'href' => '#', 'icon' => 'database', 'lang_key' => 'db_tracker', 'children' => [
                ['label' => 'Schema Info', 'route' => 'db-tracker.schema', 'href' => route('db-tracker.schema'), 'lang_key' => 'schema_info'],
                ['label' => 'Data & Activity', 'route' => 'db-tracker.data', 'href' => route('db-tracker.data'), 'lang_key' => 'data_activity'],
                ['label' => 'Performance', 'route' => 'db-tracker.performance', 'href' => route('db-tracker.performance'), 'lang_key' => 'performance'],
                ['label' => 'Security', 'route' => 'db-tracker.security', 'href' => route('db-tracker.security'), 'lang_key' => 'security_users'],
                ['label' => 'Backups & Recovery', 'route' => 'db-tracker.backups', 'href' => route('db-tracker.backups'), 'lang_key' => 'backups'],
            ]], $isDbTrackerEnabled ? [] : ['hidden' => true]),
            ['label' => 'Software', 'route' => 'software.*', 'href' => '#', 'icon' => 'software', 'lang_key' => 'software', 'children' => [
                ['label' => 'Software List', 'route' => 'software.index', 'href' => route('software.index'), 'lang_key' => 'software_list'],
                ['label' => 'Add Software', 'route' => 'software.create', 'href' => route('software.create'), 'lang_key' => 'software_add'],
            ]],
            ['label' => 'Admin Directory', 'route' => 'admin-directory.*', 'href' => '#', 'icon' => 'users', 'lang_key' => 'admin_directory', 'children' => [
                ['label' => 'Admin List', 'route' => 'admin-directory.index', 'href' => route('admin-directory.index'), 'lang_key' => 'admin_list'],
                ['label' => 'Add Admin', 'route' => 'admin-directory.create', 'href' => route('admin-directory.create'), 'lang_key' => 'admin_add'],
            ]],
            ['label' => 'Security', 'route' => 'security', 'href' => route('security'), 'icon' => 'shield', 'lang_key' => 'security'],
            ['label' => 'Settings', 'route' => 'settings', 'href' => route('settings'), 'icon' => 'gear', 'lang_key' => 'settings'],
        ],
    ];

    $icons = [
        'grid' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'stack' => '<path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>',
        'bar-chart' => '<path d="M4 20V10"/><path d="M12 20V4"/><path d="M20 20v-6"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'credit-card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3.5 20c0-3.6 2.5-6.5 5.5-6.5s5.5 2.9 5.5 6.5"/><circle cx="17.5" cy="9" r="2.2"/><path d="M15.8 13.8c2.4.4 4.2 2.7 4.2 5.6"/>',
        'shield' => '<path d="M12 3 5 6v5c0 4.5 3 7.7 7 9 4-1.3 7-4.5 7-9V6l-7-3Z"/>',
        'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 5V4M12 20v-1M5 12H4M20 12h-1M7.5 7.5l-.7-.7M17.2 17.2l-.7-.7M7.5 16.5l-.7.7M17.2 6.8l-.7.7"/>',
        'database' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
        'software' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="m8 10 2 2-2 2"/><path d="M12 14h4"/>',
    ];
@endphp

<style>
    #cms-main-sidebar { transition: width 0.3s ease; }
    #cms-main-sidebar.cms-collapsed { width: 4.5rem; padding-left: 0.75rem; padding-right: 0.75rem; }
    #cms-main-sidebar.cms-collapsed .sidebar-text { display: none; }
    #cms-main-sidebar.cms-collapsed .sidebar-logo { display: none; }
    #cms-main-sidebar.cms-collapsed .sidebar-footer { display: none; }
    #cms-main-sidebar.cms-collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
    #cms-main-sidebar.cms-collapsed .dropdown-arrow { display: none; }
</style>

<aside id="cms-main-sidebar" class="flex w-64 flex-none flex-col gap-6 border-r border-gray-200 bg-white px-4 py-6 dark:border-white/10 dark:bg-gray-900 overflow-y-auto">
    <div class="flex items-center justify-between px-2">
        <a href="{{ route('dashboard') }}" class="sidebar-logo flex items-baseline gap-2">
            <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">Eios Cms</span>
        </a>
        <button onclick="document.getElementById('cms-main-sidebar').classList.toggle('cms-collapsed')" class="p-1 rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Thu gọn/Mở rộng Sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <nav class="flex flex-col gap-1">
        @foreach ($navGroups as $groupIndex => $navItems)
            @if ($groupIndex > 0)
                <hr class="my-2 border-gray-100 dark:border-white/10">
            @endif

            @foreach ($navItems as $item)
                @if (empty($item['hidden']))
                @php 
                    $isActive = $item['route'] && (str_ends_with($item['route'], '.*') ? request()->routeIs($item['route']) : request()->routeIs($item['route']));
                @endphp
                
                @if(isset($item['children']))
                    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="flex flex-col">
                        <button
                            @click="open = !open"
                            @class([
                                'nav-item flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors w-full',
                                'bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark' => $isActive,
                                'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' => ! $isActive,
                            ])
                            title="{{ $item['label'] }}"
                        >
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-[18px] w-[18px] flex-none">
                                    {!! $icons[$item['icon']] !!}
                                </svg>
                                <span class="sidebar-text truncate">{{ __("messages.{$item['lang_key']}") }}</span>
                            </div>
                            <svg class="w-4 h-4 dropdown-arrow transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1 sidebar-text">
                            @foreach($item['children'] as $child)
                                @php $isChildActive = request()->routeIs($child['route']); @endphp
                                <a href="{{ $child['href'] }}" @class([
                                    'block rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                    'bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark' => $isChildActive,
                                    'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5' => ! $isChildActive,
                                ])>
                                    {{ __("messages.{$child['lang_key']}") }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a
                        href="{{ $item['href'] }}"
                        @class([
                            'nav-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark' => $isActive,
                            'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' => ! $isActive,
                        ])
                        title="{{ $item['label'] }}"
                    >
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-[18px] w-[18px] flex-none">
                                {!! $icons[$item['icon']] !!}
                            </svg>
                            <span class="sidebar-text truncate">{{ __("messages.{$item['lang_key']}") }}</span>
                        </div>
                    </a>
                @endif
                @endif
            @endforeach
        @endforeach
    </nav>

    <div class="sidebar-footer mt-auto rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 text-xs text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
        Signed in as
        <span class="block font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()?->name }}</span>
    </div>
</aside>
