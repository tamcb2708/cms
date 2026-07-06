<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): View
    {
        $tenantStats = $this->dashboardService->getTenantStats();
        $growth = $this->dashboardService->getTenantGrowth();
        $recentTenants = $this->dashboardService->getRecentTenants();
        $team = $this->dashboardService->getTeamStats();

        return view('dashboard.index', compact('tenantStats', 'growth', 'recentTenants', 'team'));
    }
}
