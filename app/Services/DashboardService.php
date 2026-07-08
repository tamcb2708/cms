<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class DashboardService
{
    public function getTenantStats(): array
    {
        return [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'pending' => Tenant::where('status', 'pending')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
        ];
    }

    /**
     * Overview cards: monthly revenue, active subscriptions, new tenants, total customers.
     * Each figure is compared against the previous calendar month.
     */
    public function getOverviewStats(): array
    {
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $revenueThisMonth = (float) Subscription::where('started_at', '>=', $thisMonthStart)->sum('amount');
        $revenueLastMonth = (float) Subscription::whereBetween('started_at', [$lastMonthStart, $lastMonthEnd])->sum('amount');

        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $activeSubscriptionsLastMonth = Subscription::where('status', 'active')
            ->where('started_at', '<=', $lastMonthEnd)
            ->count();

        $newTenantsThisMonth = Tenant::where('created_at', '>=', $thisMonthStart)->count();
        $newTenantsLastMonth = Tenant::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $totalCustomers = Tenant::count();
        $totalCustomersLastMonth = Tenant::where('created_at', '<=', $lastMonthEnd)->count();

        return [
            'revenue' => $this->withChange($revenueThisMonth, $revenueLastMonth),
            'activeSubscriptions' => $this->withChange($activeSubscriptions, $activeSubscriptionsLastMonth),
            'newTenants' => $this->withChange($newTenantsThisMonth, $newTenantsLastMonth),
            'totalCustomers' => $this->withChange($totalCustomers, $totalCustomersLastMonth),
        ];
    }

    private function withChange(int|float $current, int|float $previous): array
    {
        $changePercent = $previous > 0
            ? round((($current - $previous) / $previous) * 100, 1)
            : ($current > 0 ? 100.0 : 0.0);

        return [
            'value' => $current,
            'previous' => $previous,
            'changePercent' => $changePercent,
        ];
    }

    public function getProducts(): array
    {
        return Product::query()->orderBy('name')->get(['id', 'name', 'code'])->toArray();
    }

    /**
     * Revenue series bucketed by day/week/month, optionally filtered by product and billing cycle.
     *
     * @return array{buckets: array<int, array{label: string, value: float}>, total: float}
     */
    public function getRevenueSeries(string $granularity = 'month', ?string $productCode = null, ?string $billingCycle = null): array
    {
        [$periods, $start] = match ($granularity) {
            'day' => [30, now()->subDays(29)->startOfDay()],
            'week' => [12, now()->subWeeks(11)->startOfWeek()],
            default => [12, now()->subMonthsNoOverflow(11)->startOfMonth()],
        };

        $query = Subscription::query()
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->join('products', 'products.id', '=', 'plans.product_id')
            ->where('subscriptions.started_at', '>=', $start);

        if ($productCode) {
            $query->where('products.code', $productCode);
        }

        if ($billingCycle) {
            $query->where('plans.billing_cycle', $billingCycle);
        }

        $rows = $query
            ->selectRaw('subscriptions.started_at as started_at, subscriptions.amount as amount')
            ->get();

        $sums = [];
        foreach ($rows as $row) {
            $date = Carbon::parse($row->started_at);
            $key = match ($granularity) {
                'day' => $date->format('Y-m-d'),
                'week' => $date->format('o-W'),
                default => $date->format('Y-m'),
            };
            $sums[$key] = ($sums[$key] ?? 0) + (float) $row->amount;
        }

        $buckets = [];
        $total = 0.0;

        for ($i = $periods - 1; $i >= 0; $i--) {
            [$key, $label] = match ($granularity) {
                'day' => [
                    now()->subDays($i)->format('Y-m-d'),
                    now()->subDays($i)->format('d/m'),
                ],
                'week' => [
                    now()->subWeeks($i)->startOfWeek()->format('o-W'),
                    'Tuần ' . now()->subWeeks($i)->startOfWeek()->format('W'),
                ],
                default => [
                    now()->subMonthsNoOverflow($i)->format('Y-m'),
                    now()->subMonthsNoOverflow($i)->format('m/Y'),
                ],
            };

            $value = (float) ($sums[$key] ?? 0);
            $total += $value;
            $buckets[] = ['label' => $label, 'value' => $value];
        }

        return ['buckets' => $buckets, 'total' => $total];
    }

    public function getRecentTenants(int $limit = 5)
    {
        return Tenant::query()->latest()->limit($limit)->get();
    }

    public function getTeamStats(): array
    {
        return [
            'users' => User::count(),
            'super_admins' => Role::where('name', 'Super Admin')->first()?->users()->count() ?? 0,
            'tenant_admins' => Role::where('name', 'Tenant Admin')->first()?->users()->count() ?? 0,
            'roles' => Role::count(),
        ];
    }

    /**
     * Tenants created per day for the last 7 days, oldest first.
     *
     * @return array<int, array{label: string, value: int}>
     */
    public function getTenantGrowth(): array
    {
        $counts = Tenant::query()
            ->selectRaw('DATE(created_at) as day, count(*) as aggregate')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = [
                'label' => $date->format('D'),
                'value' => (int) ($counts[$date->format('Y-m-d')] ?? 0),
            ];
        }

        return $days;
    }
}
