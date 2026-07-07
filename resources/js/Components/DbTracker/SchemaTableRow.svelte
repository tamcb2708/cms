<script>
    import { route } from '../../lib/route.js';

    let { tableName } = $props();

    let expanded = $state(false);
    let loaded = $state(false);
    let loading = $state(false);
    let details = $state(null);

    async function toggle() {
        expanded = !expanded;
        if (expanded && !loaded) {
            loading = true;
            const res = await fetch(`${route('db-tracker.schema')}/${tableName}/details`);
            details = await res.json();
            loaded = true;
            loading = false;
        }
    }

    function isPk(name) {
        return details?.pks?.includes(name);
    }

    function fk(name) {
        return details?.fks?.find((f) => f.column_name === name);
    }
</script>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 transition-colors hover:border-accent-400 dark:border-gray-700 dark:bg-gray-700/30">
    <button onclick={toggle} class="flex w-full items-center justify-between bg-white p-4 transition-colors hover:bg-gray-50 focus:outline-none dark:bg-gray-800 dark:hover:bg-gray-700/50">
        <div class="flex items-center gap-2 font-medium text-gray-900 dark:text-white">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            {tableName}
        </div>
        <svg class="h-5 w-5 transform text-gray-400 transition-transform duration-200 {expanded ? 'rotate-180' : ''}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
    </button>

    {#if expanded}
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">
            {#if loading}
                <div class="py-6 text-center text-gray-500">
                    <svg class="mx-auto mb-2 h-6 w-6 animate-spin text-accent-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Đang tải thông tin...
                </div>
            {:else if loaded && details}
                <h4 class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cột dữ liệu</h4>
                <div class="mb-4 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tên cột</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Kiểu dữ liệu</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Bắt buộc</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Giá trị mặc định</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            {#each details.columns as col}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="whitespace-nowrap px-4 py-2 text-sm font-medium {isPk(col.column_name) ? 'text-accent-600 dark:text-accent-400' : 'text-gray-900 dark:text-white'}">
                                        {col.column_name}
                                        {#if isPk(col.column_name)}
                                            <span class="ml-1 inline-flex items-center rounded bg-yellow-100 px-1.5 py-0.5 text-[10px] font-medium text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400" title="Primary Key">PK</span>
                                        {/if}
                                        {#if fk(col.column_name)}
                                            <span class="ml-1 inline-flex items-center rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-400" title="Foreign Key">FK</span>
                                        {/if}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{col.data_type}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        {#if col.is_nullable === 'NO'}
                                            <span class="font-bold text-red-500">Yes</span>
                                        {:else}
                                            No
                                        {/if}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{col.column_default || 'NULL'}</td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>

                {#if details.fks && details.fks.length > 0}
                    <div>
                        <h4 class="mb-2 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Khoá ngoại</h4>
                        <ul class="space-y-1.5">
                            {#each details.fks as f}
                                <li class="flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <span><strong>{f.column_name}</strong> tham chiếu tới <strong>{f.foreign_table_name}</strong>.<span>{f.foreign_column_name}</span></span>
                                </li>
                            {/each}
                        </ul>
                    </div>
                {/if}
            {/if}
        </div>
    {/if}
</div>
