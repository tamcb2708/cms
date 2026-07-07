<script>
    let { tenantStats } = $props();

    const tiles = $derived([
        { label: 'Total tenants', value: tenantStats.total, border: 'border-t-indigo-500' },
        { label: 'Active', value: tenantStats.active, border: 'border-t-green-500' },
        { label: 'Pending', value: tenantStats.pending, border: 'border-t-yellow-500' },
        { label: 'Suspended', value: tenantStats.suspended, border: 'border-t-red-500' },
    ]);
</script>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    {#each tiles as tile}
        <div class="rounded-xl border border-t-4 border-gray-100 {tile.border} bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{tile.label}</div>
            <div class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{tile.value}</div>
        </div>
    {/each}
</div>
