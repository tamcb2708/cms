<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Tenant status</h2>
    <p class="mt-0.5 text-xs text-gray-400">Breakdown by current status</p>

    <div class="mt-4 flex items-center gap-6">
        <div
            class="relative flex-none"
            id="tenant-status-chart"
            data-counts="{{ json_encode([
                'active' => $tenantStats['active'],
                'pending' => $tenantStats['pending'],
                'suspended' => $tenantStats['suspended'],
            ]) }}"
        ></div>
        <div class="flex flex-1 flex-col gap-2 text-sm">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 flex-none rounded-full bg-green-500"></span>
                <span class="flex-1 text-gray-700 dark:text-gray-300">Active</span>
                <span class="font-medium text-gray-950 dark:text-white">{{ $tenantStats['active'] }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 flex-none rounded-full bg-yellow-500"></span>
                <span class="flex-1 text-gray-700 dark:text-gray-300">Pending</span>
                <span class="font-medium text-gray-950 dark:text-white">{{ $tenantStats['pending'] }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 flex-none rounded-full bg-red-500"></span>
                <span class="flex-1 text-gray-700 dark:text-gray-300">Suspended</span>
                <span class="font-medium text-gray-950 dark:text-white">{{ $tenantStats['suspended'] }}</span>
            </div>
        </div>
    </div>
</div>
