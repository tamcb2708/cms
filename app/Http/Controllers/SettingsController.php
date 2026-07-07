<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    private SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index(Request $request): Response
    {
        $tabs = $this->settingsService->getTabsStructure();
        $structure = $this->settingsService->getConfigStructure();
        $section = $request->query('section', 'general');

        if (!isset($structure[$section])) {
            $section = array_key_first($structure);
        }

        // Parse scope from format like "websites_1"
        $scopeParam = $request->query('scope', 'default_0');
        [$scope, $scopeId] = explode('_', $scopeParam) + ['default', 0];

        $scopes = [
            ['value' => 'default_0', 'label' => 'Global (Mặc định)'],
            ['value' => 'websites_1', 'label' => 'Website: Việt Nam'],
            ['value' => 'websites_2', 'label' => 'Website: Mỹ (Global)'],
        ];

        $data = $this->settingsService->getSettingsData($section, $scope, $scopeId);
        $values = $data['values'];
        $useSystem = $data['useSystem'];

        $tree = $this->settingsService->getTreeStructure();
        $activeTab = $structure[$section]['tab'] ?? 'general_tab';

        $props = compact('tabs', 'tree', 'activeTab', 'structure', 'section', 'scopeParam', 'scope', 'scopeId', 'scopes', 'values', 'useSystem');

        if ($section === 'permissions') {
            $props = array_merge($props, app(RolePermissionController::class)->permissionsData());
        }

        return Inertia::render('Settings/Index', $props);
    }

    public function save(Request $request)
    {
        $section = $request->input('section', 'general');
        $scopeParam = $request->input('scope_param', 'default_0');
        [$scope, $scopeId] = explode('_', $scopeParam) + ['default', 0];
        
        $config = $request->input('config', []);
        $useSystem = $request->input('use_system', []);
        
        $success = $this->settingsService->saveSettings($section, $scope, $scopeId, $config, $useSystem);
        
        if (!$success) {
            return back()->withErrors(['error' => 'Invalid section']);
        }
        
        return redirect()->route('settings', ['section' => $section, 'scope' => $scopeParam])
            ->with('success', 'Đã lưu cấu hình thành công!');
    }
}
