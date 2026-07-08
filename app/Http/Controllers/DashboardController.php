<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request): Response
    {
        $granularity = $request->string('granularity', 'month')->value();
        $productCode = $request->string('product')->value() ?: null;
        $billingCycle = $request->string('billing_cycle')->value() ?: null;

        if (! in_array($granularity, ['day', 'week', 'month'], true)) {
            $granularity = 'month';
        }

        return Inertia::render('Dashboard/Index', [
            'tenantStats' => $this->dashboardService->getTenantStats(),
            'overview' => $this->dashboardService->getOverviewStats(),
            'growth' => $this->dashboardService->getTenantGrowth(),
            'recentTenants' => $this->dashboardService->getRecentTenants(),
            'team' => $this->dashboardService->getTeamStats(),
            'products' => $this->dashboardService->getProducts(),
            'revenue' => $this->dashboardService->getRevenueSeries($granularity, $productCode, $billingCycle),
            'revenueFilters' => [
                'granularity' => $granularity,
                'product' => $productCode,
                'billing_cycle' => $billingCycle,
            ],
            'tenantsUrl' => route('tenants.index'),
        ]);
    }
}
