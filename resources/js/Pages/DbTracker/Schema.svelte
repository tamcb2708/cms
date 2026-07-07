<script>
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import PageHeader from '../../Components/DbTracker/PageHeader.svelte';
    import SchemaTableRow from '../../Components/DbTracker/SchemaTableRow.svelte';
    import MermaidErd from '../../Components/DbTracker/MermaidErd.svelte';

    let { creds, tables = [] } = $props();

    let viewMode = $state('list');
</script>

<AppLayout title="DB Tracker - Schema Info">
    <div class="flex h-full flex-col bg-gray-50 p-6 dark:bg-gray-900">
        <PageHeader title="Schema Info" {creds}>
            {#snippet children()}
                <div class="flex rounded-lg bg-gray-200 p-1 dark:bg-gray-700">
                    <button
                        onclick={() => (viewMode = 'list')}
                        class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors {viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-600 dark:text-white' : 'text-gray-500 hover:bg-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white'}"
                    >
                        Danh sách bảng
                    </button>
                    <button
                        onclick={() => (viewMode = 'erd')}
                        class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors {viewMode === 'erd' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-600 dark:text-white' : 'text-gray-500 hover:bg-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white'}"
                    >
                        Sơ đồ ERD
                    </button>
                </div>
            {/snippet}
        </PageHeader>

        {#if viewMode === 'list'}
            <div class="flex flex-1 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <h3 class="font-medium">Danh sách bảng</h3>
                    <span class="rounded-full bg-accent-100 px-2.5 py-1 text-xs font-medium text-accent-700 dark:bg-accent-500/20 dark:text-accent-300">{tables.length} Tables</span>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="flex flex-col gap-3">
                        {#each tables as table}
                            <SchemaTableRow tableName={table.table_name} />
                        {/each}
                    </div>
                </div>
            </div>
        {:else}
            <div class="flex flex-1 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <h3 class="font-medium">Sơ đồ quan hệ (ERD)</h3>
                </div>
                <div class="flex flex-1 items-start justify-center overflow-auto bg-gray-50 p-4 dark:bg-gray-900/50">
                    <MermaidErd />
                </div>
            </div>
        {/if}
    </div>
</AppLayout>
