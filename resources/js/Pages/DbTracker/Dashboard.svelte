<script>
    import { onMount, onDestroy } from 'svelte';
    import { router } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import PageHeader from '../../Components/DbTracker/PageHeader.svelte';
    import LogsTable from '../../Components/DbTracker/LogsTable.svelte';
    import { route } from '../../lib/route.js';
    import { addToast } from '../../stores/toast.svelte.js';

    let { creds, isInitialized, tables = [] } = $props();

    let search = $state('');
    let sidebarCollapsed = $state(false);
    let logs = $state([]);
    let logsStatus = $state('not_connected');
    let synced = $state(true);
    let pollTimer;

    const filteredTables = $derived(
        tables.filter((t) => t.name.toLowerCase().includes(search.toLowerCase())),
    );

    async function runAction(action, table = null) {
        const formData = new FormData();
        formData.append('action', action);
        if (table) formData.append('table', table);

        try {
            const res = await fetch(route('db-tracker.action'), {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '' },
            });
            const data = await res.json();
            if (data.success) {
                router.reload();
            } else {
                addToast({ title: 'Lỗi', message: data.message || 'Lỗi thực thi', type: 'error' });
            }
        } catch (err) {
            addToast({ title: 'Lỗi', message: `Có lỗi xảy ra kết nối Server: ${err.message}`, type: 'error' });
        }
    }

    function clearLogs() {
        if (confirm('Bạn có chắc chắn muốn xoá toàn bộ log không?')) {
            runAction('clear');
        }
    }

    async function fetchLogs() {
        try {
            const res = await fetch(`${route('db-tracker.logs')}?_t=${Date.now()}`, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();
            logs = data.logs;
            logsStatus = data.status;
            synced = true;
        } catch (err) {
            synced = false;
        }
    }

    onMount(() => {
        if (isInitialized) {
            fetchLogs();
            pollTimer = setInterval(fetchLogs, 3000);
        }
    });

    onDestroy(() => {
        if (pollTimer) clearInterval(pollTimer);
    });
</script>

<AppLayout title="DB Tracker - Data & Activity">
    <div class="flex h-full flex-col bg-gray-50 p-6 dark:bg-gray-900">
        <PageHeader title="Data & Activity" {creds} />

        {#if !isInitialized}
            <div class="flex flex-1 items-center justify-center">
                <div class="w-full max-w-md rounded-xl border border-gray-100 bg-white p-8 text-center shadow-sm dark:border-white/5 dark:bg-gray-800">
                    <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">Hệ thống chưa được khởi tạo</h3>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Khởi tạo bảng audit log và trigger để bắt đầu theo dõi thay đổi dữ liệu.</p>
                    <button onclick={() => runAction('init')} class="rounded-lg bg-accent-600 px-4 py-2 font-medium text-white shadow-sm transition-colors hover:bg-accent-700">
                        Khởi tạo hệ thống
                    </button>
                </div>
            </div>
        {:else}
            <div class="dbt-grid">
                <div class="dbt-card dbt-col-1" class:dbt-collapsed={sidebarCollapsed}>
                    <div class="dbt-header flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <h3 class="flex items-center gap-2 font-medium">Danh sách bảng</h3>
                            <button onclick={() => router.reload()} class="dbt-text-accent flex items-center gap-1 text-xs font-medium">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Tải lại
                            </button>
                        </div>
                        <input
                            type="text"
                            bind:value={search}
                            placeholder="Tìm bảng..."
                            class="w-full rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        />
                    </div>
                    <div class="dbt-scroll-y flex flex-col gap-2 p-4" style="flex: 1;">
                        {#each filteredTables as t}
                            <div class="dbt-item flex items-center justify-between rounded-lg p-3">
                                <div>
                                    <div class="text-sm font-medium">{t.name}</div>
                                    {#if t.tracked}
                                        <span class="mt-1 inline-flex items-center rounded bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-800 dark:bg-green-500/10 dark:text-green-400">Đang theo dõi</span>
                                    {:else}
                                        <span class="mt-1 inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-800 dark:bg-gray-500/10 dark:text-gray-400">Chưa theo dõi</span>
                                    {/if}
                                </div>
                                <div>
                                    {#if t.tracked}
                                        <button onclick={() => runAction('untrack', t.name)} class="dbt-text-red text-xs font-medium">Tắt</button>
                                    {:else}
                                        <button onclick={() => runAction('track', t.name)} class="dbt-text-green text-xs font-medium">Bật</button>
                                    {/if}
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>

                <div class="dbt-card dbt-col-2">
                    <div class="dbt-header flex items-center justify-between">
                        <h3 class="flex items-center gap-2 font-medium">
                            <button onclick={() => (sidebarCollapsed = !sidebarCollapsed)} class="rounded p-1 transition-colors hover:bg-gray-200 dark:hover:bg-gray-700" title="Bật/Tắt danh sách bảng">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            Hoạt động gần đây
                            <span class="flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[10px] font-medium {synced ? 'dbt-badge-green' : 'dbt-badge-red'}">
                                <span class="h-1.5 w-1.5 rounded-full {synced ? 'animate-pulse bg-green-500' : 'bg-red-500'}"></span>
                                {synced ? 'Đồng bộ' : 'Mất kết nối'}
                            </span>
                        </h3>
                        <div class="flex items-center gap-4">
                            <button onclick={fetchLogs} class="dbt-text-accent flex items-center gap-1 text-xs font-medium">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Tải lại
                            </button>
                            <button onclick={clearLogs} class="dbt-text-red text-xs font-medium">Xoá log</button>
                        </div>
                    </div>
                    <div class="dbt-table-wrapper">
                        <table class="dbt-table">
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider">Thời gian</th>
                                    <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider">Bảng</th>
                                    <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider">Hành động</th>
                                    <th class="dbt-table-th w-full p-3 text-xs font-medium uppercase tracking-wider">Dữ liệu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <LogsTable status={logsStatus} {logs} />
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</AppLayout>
