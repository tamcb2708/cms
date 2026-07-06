<?php

namespace App\Services;

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
