<script>
    let { tenantStats } = $props();

    const size = 120, cx = size / 2, cy = size / 2, r = 42, stroke = 16;
    const circumference = 2 * Math.PI * r;

    const donut = $derived.by(() => {
        const entries = [
            { key: 'active', color: '#22c55e', value: tenantStats.active || 0 },
            { key: 'pending', color: '#eab308', value: tenantStats.pending || 0 },
            { key: 'suspended', color: '#ef4444', value: tenantStats.suspended || 0 },
        ];
        const total = entries.reduce((s, e) => s + e.value, 0);

        let offset = 0;
        const arcs = total > 0
            ? entries.map((e) => {
                  const frac = e.value / total;
                  const dash = frac * circumference;
                  const arc = {
                      color: e.color,
                      dasharray: `${Math.max(dash - 1.5, 0)} ${circumference - dash + 1.5}`,
                      dashoffset: (-offset).toFixed(2),
                  };
                  offset += dash;
                  return arc;
              })
            : [];

        return { arcs, total };
    });
</script>

<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Tenant status</h2>
    <p class="mt-0.5 text-xs text-gray-400">Breakdown by current status</p>

    <div class="mt-4 flex items-center gap-6">
        <div class="relative flex-none">
            <svg viewBox="0 0 {size} {size}" width={size} height={size}>
                <circle {cx} {cy} {r} fill="none" stroke="currentColor" class="text-gray-100 dark:text-white/10" stroke-width={stroke} />
                {#each donut.arcs as arc}
                    <circle
                        {cx} {cy} {r} fill="none"
                        stroke={arc.color}
                        stroke-width={stroke}
                        stroke-dasharray={arc.dasharray}
                        stroke-dashoffset={arc.dashoffset}
                        transform="rotate(-90 {cx} {cy})"
                        stroke-linecap="round"
                    />
                {/each}
            </svg>
            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-lg font-bold text-gray-950 dark:text-white">{donut.total}</span>
                <span class="text-[9px] text-gray-400">tenants</span>
            </div>
        </div>
        <div class="flex flex-1 flex-col gap-2 text-sm">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 flex-none rounded-full bg-green-500"></span>
                <span class="flex-1 text-gray-700 dark:text-gray-300">Active</span>
                <span class="font-medium text-gray-950 dark:text-white">{tenantStats.active}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 flex-none rounded-full bg-yellow-500"></span>
                <span class="flex-1 text-gray-700 dark:text-gray-300">Pending</span>
                <span class="font-medium text-gray-950 dark:text-white">{tenantStats.pending}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 flex-none rounded-full bg-red-500"></span>
                <span class="flex-1 text-gray-700 dark:text-gray-300">Suspended</span>
                <span class="font-medium text-gray-950 dark:text-white">{tenantStats.suspended}</span>
            </div>
        </div>
    </div>
</div>
