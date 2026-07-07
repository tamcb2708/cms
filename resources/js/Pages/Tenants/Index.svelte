<script>
    import { Link, router } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import { route } from '../../lib/route.js';

    let { tenants, filters = {} } = $props();

    let search = $state(filters.search ?? '');

    const badgeClasses = {
        active: 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        pending: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
        suspended: 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    };

    function badgeClass(status) {
        return badgeClasses[status] ?? 'bg-gray-100 text-gray-600';
    }

    function capitalize(value) {
        return value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
    }

    function submitSearch(e) {
        e.preventDefault();
        router.get(route('tenants.index'), { search }, { preserveState: true, replace: true });
    }

    function destroy(tenant) {
        if (confirm(`Xoá tenant "${tenant.name}"?`)) {
            router.delete(route('tenants.destroy', tenant.id));
        }
    }
</script>

<AppLayout title="Tenants">
    <div class="mx-auto max-w-6xl py-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tenants</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Quản lý các workspace / subscription của khách hàng.</p>
            </div>
            <Link href={route('tenants.create')} class="flex items-center gap-2 rounded-lg bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm tenant
            </Link>
        </div>

        <form onsubmit={submitSearch} class="mb-4">
            <div class="relative max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                </div>
                <input type="text" bind:value={search} placeholder="Tìm theo tên, domain..." class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Domain</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Database</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    {#each tenants.data as tenant}
                        <tr class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{tenant.name}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{tenant.domain}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{tenant.database_name ?? '—'}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {badgeClass(tenant.status)}">{capitalize(tenant.status)}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <Link href={route('tenants.edit', tenant.id)} class="text-accent-600 hover:text-accent-800 dark:text-accent-400 dark:hover:text-accent-300">Sửa</Link>
                                    <button onclick={() => destroy(tenant)} class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Xoá</button>
                                </div>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Chưa có tenant nào.</td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        {#if tenants.links && tenants.links.length > 3}
            <div class="mt-4 flex items-center justify-center gap-1">
                {#each tenants.links as link}
                    {#if link.url}
                        <Link href={link.url} class="rounded-lg px-3 py-1.5 text-sm {link.active ? 'bg-accent-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'}">{@html link.label}</Link>
                    {:else}
                        <span class="rounded-lg px-3 py-1.5 text-sm text-gray-400">{@html link.label}</span>
                    {/if}
                {/each}
            </div>
        {/if}
    </div>
</AppLayout>
