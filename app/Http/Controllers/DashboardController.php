<?php

namespace App\Http\Controllers;

use App\Filament\Resources\TenantResource;
use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): Response
    {
        return Inertia::render('Dashboard/Index', [
            'tenantStats' => $this->dashboardService->getTenantStats(),
            'growth' => $this->dashboardService->getTenantGrowth(),
            'recentTenants' => $this->dashboardService->getRecentTenants(),
            'team' => $this->dashboardService->getTeamStats(),
            'tenantsUrl' => TenantResource::getUrl(),
        ]);
    }
}
