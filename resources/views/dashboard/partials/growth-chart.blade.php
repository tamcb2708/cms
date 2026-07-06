<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Tenant growth</h2>
    <p class="mt-0.5 text-xs text-gray-400">New tenants over the last 7 days</p>
    <div class="relative mt-4" id="tenant-growth-chart" data-points="{{ json_encode($growth) }}"></div>
</div>
