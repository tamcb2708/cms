<?php

namespace App\Http\Middleware;

use App\Models\CoreConfig;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'job_title' => $user->job_title,
                    'locale' => $user->locale,
                ] : null,
            ],
            'locale' => app()->getLocale(),
            'dbTrackerStatus' => CoreConfig::where('path', 'database/tracker/status')->value('value') ?? 'pending',
            'navGroups' => $user ? $this->buildNav($request) : [],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'success_profile' => fn () => $request->session()->get('success_profile'),
                'success_password' => fn () => $request->session()->get('success_password'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }

    /**
     * Build the sidebar navigation tree with resolved hrefs, translated
     * labels and active-state, mirroring the logic previously inlined in
     * resources/views/layouts/partials/sidebar.blade.php.
     */
    private function buildNav(Request $request): array
    {
        $isDbTrackerEnabled = CoreConfig::getValue('database/tracker/enable', 'default', 0) == '1';
        $user = $request->user();

        $groups = [
            [
                ['label' => __('messages.dashboard'), 'route' => 'dashboard', 'href' => route('dashboard'), 'icon' => 'grid', 'module' => 'dashboard'],
                ['label' => __('messages.content_management'), 'route' => 'content-management', 'href' => route('content-management'), 'icon' => 'folder', 'module' => 'content'],
                ['label' => __('messages.reports'), 'route' => 'reports', 'href' => route('reports'), 'icon' => 'bar-chart', 'module' => 'reports'],
                ['label' => __('messages.email_campaigns'), 'route' => 'email-campaigns', 'href' => route('email-campaigns'), 'icon' => 'mail'],
                ['label' => __('messages.workspace_subscription'), 'route' => 'tenants.*', 'href' => route('tenants.index'), 'icon' => 'credit-card'],
            ],
            array_values(array_filter([
                $isDbTrackerEnabled ? [
                    'label' => __('messages.db_tracker'), 'route' => 'db-tracker.*', 'href' => '#', 'icon' => 'database', 'module' => 'db-tracker',
                    'children' => [
                        ['label' => __('messages.schema_info'), 'route' => 'db-tracker.schema', 'href' => route('db-tracker.schema')],
                        ['label' => __('messages.data_activity'), 'route' => 'db-tracker.data', 'href' => route('db-tracker.data')],
                        ['label' => __('messages.performance'), 'route' => 'db-tracker.performance', 'href' => route('db-tracker.performance')],
                        ['label' => __('messages.security_users'), 'route' => 'db-tracker.security', 'href' => route('db-tracker.security')],
                        ['label' => __('messages.backups'), 'route' => 'db-tracker.backups', 'href' => route('db-tracker.backups')],
                    ],
                ] : null,
                [
                    'label' => __('messages.software'), 'route' => 'software.*', 'href' => '#', 'icon' => 'software',
                    'children' => [
                        ['label' => __('messages.software_list'), 'route' => 'software.index', 'href' => route('software.index')],
                        ['label' => __('messages.software_add'), 'route' => 'software.create', 'href' => route('software.create')],
                    ],
                ],
                [
                    'label' => __('messages.admin_directory'), 'route' => 'admin-directory.*', 'href' => '#', 'icon' => 'users', 'module' => 'admin-directory',
                    'children' => [
                        ['label' => __('messages.admin_list'), 'route' => 'admin-directory.index', 'href' => route('admin-directory.index')],
                        ['label' => __('messages.admin_add'), 'route' => 'admin-directory.create', 'href' => route('admin-directory.create')],
                    ],
                ],
                ['label' => __('messages.security'), 'route' => 'security', 'href' => route('security'), 'icon' => 'shield'],
                ['label' => __('messages.settings'), 'route' => 'settings', 'href' => route('settings'), 'icon' => 'gear', 'module' => 'settings'],
            ])),
        ];

        // Only show modules the current admin has "view" permission for.
        // Items with no 'module' key are shown to every authenticated admin.
        foreach ($groups as &$items) {
            $items = array_values(array_filter($items, function ($item) use ($user) {
                return ! isset($item['module']) || $user->hasModulePermission($item['module'], 'view');
            }));
        }

        foreach ($groups as &$items) {
            foreach ($items as &$item) {
                $item['active'] = $item['route'] && $request->routeIs($item['route']);

                if (isset($item['children'])) {
                    foreach ($item['children'] as &$child) {
                        $child['active'] = $request->routeIs($child['route']);
                    }
                }
            }
        }

        return $groups;
    }
}
