<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * Dev-only helper to populate sample subscriptions so the revenue dashboard
 * has data to render. Not wired into DatabaseSeeder — run explicitly:
 * php artisan db:seed --class=DemoSubscriptionsSeeder
 */
class DemoSubscriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();
        $plans = Plan::all();

        if ($tenants->isEmpty() || $plans->isEmpty()) {
            return;
        }

        foreach ($tenants as $tenant) {
            $monthsBack = random_int(1, 11);

            for ($i = $monthsBack; $i >= 0; $i--) {
                $plan = $plans->random();

                Subscription::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'status' => $i === 0 ? 'active' : 'expired',
                    'amount' => $plan->price,
                    'started_at' => now()->subMonthsNoOverflow($i)->startOfMonth()->addDays(random_int(0, 5)),
                    'ended_at' => $i === 0 ? null : now()->subMonthsNoOverflow($i - 1)->startOfMonth(),
                ]);
            }
        }
    }
}
