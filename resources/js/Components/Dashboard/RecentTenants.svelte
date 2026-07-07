<script>
    let { recentTenants = [], tenantsUrl = '#' } = $props();

    const badgeClasses = {
        active: 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        pending: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
        suspended: 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    };

    function badgeClass(status) {
        return badgeClasses[status] ?? 'bg-gray-100 text-gray-600';
    }

    function capitalize(value) {
        return value.charAt(0).toUpperCase() + value.slice(1);
    }
</script>

<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Recent tenants</h2>

    {#if recentTenants.length === 0}
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">No tenants yet. Create one from the Tenants page.</p>
    {:else}
        <div class="mt-3 divide-y divide-gray-100 dark:divide-white/5">
            {#each recentTenants as tenant}
                <div class="flex items-center justify-between gap-3 py-2.5">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-950 dark:text-white">{tenant.name}</p>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">{tenant.domain}</p>
                    </div>
                    <span class="flex-none rounded-full px-2.5 py-1 text-xs font-medium {badgeClass(tenant.status)}">
                        {capitalize(tenant.status)}
                    </span>
                </div>
            {/each}
        </div>
    {/if}

    <div class="mt-4 border-t border-gray-100 pt-3 dark:border-white/5">
        <a href={tenantsUrl} class="text-sm font-medium text-accent-500 hover:text-accent-600 dark:text-accent-dark">
            View all tenants →
        </a>
    </div>
</div>
