<script>
    import { onMount, onDestroy } from 'svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import PageHeader from '../../Components/DbTracker/PageHeader.svelte';
    import PerformanceChart from '../../Components/DbTracker/PerformanceChart.svelte';
    import { route } from '../../lib/route.js';

    let { creds } = $props();

    const INTERVAL_OPTIONS = [
        { value: 5000, label: '5 giây' },
        { value: 30000, label: '30 giây' },
        { value: 60000, label: '1 phút' },
        { value: 3600000, label: '1 giờ' },
        { value: 21600000, label: '6 giờ' },
        { value: 86400000, label: '1 ngày' },
        { value: 604800000, label: '7 ngày' },
        { value: 259200000, label: '30 ngày' },
        { value: 7776000000, label: '3 tháng' },
        { value: 15552000000, label: '6 tháng' },
        { value: 0, label: 'Tạm dừng' },
    ];

    let synced = $state(true);
    let cacheHit = $state('--');
    let totalConn = $state('--');
    let activeQueries = $state(0);
    let idleQueries = $state(0);
    let otherQueries = $state(0);
    let dbSizePretty = $state('--');
    let dbSizePercent = $state(0);
    let waitingLocks = $state('--');
    let xactCommit = $state('--');
    let xactRollback = $state('--');
    let activities = $state([]);
    let slowQueries = $state([]);
    let refreshInterval = $state(parseInt(localStorage.getItem('db_tracker_refresh_interval') ?? '5000', 10));
    let softLimitGb = $state(parseFloat(localStorage.getItem('db_tracker_soft_limit') ?? '10'));
    let timer;

    const progressBarClass = $derived(
        dbSizePercent > 90 ? 'bg-red-500' : dbSizePercent > 75 ? 'bg-yellow-500' : 'bg-purple-500',
    );

    const refreshLabel = $derived(
        refreshInterval > 0
            ? `Làm mới: ${INTERVAL_OPTIONS.find((o) => o.value === refreshInterval)?.label ?? ''}`
            : 'Tạm dừng',
    );

    function formatDuration(sec) {
        if (sec === null || sec === undefined) return '—';
        sec = parseFloat(sec);
        if (sec < 1) return `${Math.round(sec * 1000)}ms`;
        if (sec < 60) return `${sec.toFixed(1)}s`;
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return `${m}m ${s}s`;
    }

    const numberFormat = new Intl.NumberFormat();

    async function fetchStats() {
        try {
            const res = await fetch(`${route('db-tracker.performance.stats')}?_t=${Date.now()}`);
            const data = await res.json();

            if (!data.success) {
                synced = false;
                if (timer) clearInterval(timer);
                return;
            }
            synced = true;

            cacheHit = `${data.cacheHit}%`;

            let active = 0, idle = 0, other = 0, total = 0;
            data.states.forEach((s) => {
                total += s.count;
                if (s.state === 'active') active += s.count;
                else if (s.state === 'idle') idle += s.count;
                else other += s.count;
            });
            totalConn = total;
            activeQueries = active;
            idleQueries = idle;
            otherQueries = other;

            const dbSizeBytes = data.dbStats.db_size_bytes || 0;
            dbSizePretty = data.dbStats.db_size || '--';
            const limitBytes = softLimitGb * 1024 * 1024 * 1024;
            dbSizePercent = limitBytes > 0 ? Math.min((dbSizeBytes / limitBytes) * 100, 100) : 0;

            waitingLocks = data.waitingLocks || 0;
            xactCommit = data.dbStats.xact_commit ? numberFormat.format(data.dbStats.xact_commit) : '--';
            xactRollback = data.dbStats.xact_rollback ? numberFormat.format(data.dbStats.xact_rollback) : '--';

            activities = data.activities;
            slowQueries = data.slowQueries;
        } catch (err) {
            synced = false;
        }
    }

    function setSoftLimit() {
        const limit = prompt('Định mức dung lượng CSDL (GB):', softLimitGb);
        if (limit !== null && !isNaN(limit) && limit > 0) {
            softLimitGb = parseFloat(limit);
            localStorage.setItem('db_tracker_soft_limit', String(softLimitGb));
            fetchStats();
        }
    }

    function changeInterval() {
        localStorage.setItem('db_tracker_refresh_interval', String(refreshInterval));
        if (timer) clearInterval(timer);

        if (refreshInterval > 0 && refreshInterval <= 2147483647) {
            timer = setInterval(fetchStats, refreshInterval);
            synced = true;
        }
    }

    $effect(() => {
        changeInterval();
    });

    onMount(() => {
        fetchStats();
    });

    onDestroy(() => {
        if (timer) clearInterval(timer);
    });
</script>

<AppLayout title="DB Tracker - Performance">
    <div class="flex h-full flex-col bg-gray-50 p-6 dark:bg-gray-900">
        <PageHeader title="Performance" {creds}>
            {#snippet children()}
                <div class="flex items-center rounded-full border border-gray-200 bg-white px-1 py-1 shadow-sm dark:border-white/10 dark:bg-gray-800">
                    <label for="refresh-interval" class="px-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-500">Làm mới</label>
                    <select
                        id="refresh-interval"
                        bind:value={refreshInterval}
                        class="cursor-pointer appearance-none rounded-full border-none bg-gray-50 py-1 pl-3 pr-8 text-xs font-bold text-accent-600 outline-none focus:ring-2 focus:ring-accent-500 dark:bg-gray-900 dark:text-accent-400"
                    >
                        {#each INTERVAL_OPTIONS as opt}
                            <option value={opt.value}>{opt.label}</option>
                        {/each}
                    </select>
                </div>
                <span class="flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium shadow-sm {synced ? 'border-green-200 bg-green-50 text-green-600 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400' : 'border-red-200 bg-red-50 text-red-600 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400'}">
                    <span class="h-1.5 w-1.5 rounded-full {synced ? 'animate-pulse bg-green-500' : 'bg-red-500'}"></span>
                    {synced ? 'Đồng bộ' : 'Mất kết nối'}
                </span>
            {/snippet}
        </PageHeader>

        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                <h4 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Tỷ lệ Cache Hit</h4>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{cacheHit}</div>
            </div>
            <div class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                <h4 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Tổng kết nối</h4>
                <div class="text-3xl font-bold text-accent-600">{totalConn}</div>
            </div>
            <div class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                <h4 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Truy vấn đang hoạt động</h4>
                <div class="text-3xl font-bold text-green-500">{activeQueries}</div>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div class="group relative flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                <button onclick={setSoftLimit} class="absolute right-2 top-2 rounded-md p-1.5 text-gray-400 opacity-0 transition-colors hover:bg-gray-100 hover:text-accent-500 group-hover:opacity-100 dark:hover:bg-gray-700" title="Cài đặt định mức (Soft Limit)">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
                <h4 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Dung lượng CSDL</h4>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{dbSizePretty} <span class="text-sm font-normal text-gray-400">/ {softLimitGb} GB</span></div>
                <div class="relative mt-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div class="h-1.5 rounded-full transition-all duration-500 {progressBarClass}" style="width: {dbSizePercent}%"></div>
                </div>
                <div class="mt-1.5 flex w-full justify-between text-[10px] text-gray-400">
                    <span>{dbSizePercent.toFixed(1)}%</span>
                    <span>Soft Limit</span>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                <h4 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Lock đang chờ</h4>
                <div class="text-3xl font-bold text-yellow-500">{waitingLocks}</div>
            </div>
            <div class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                <h4 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Giao dịch <span class="text-[10px] font-normal opacity-70">(C/R)</span></h4>
                <div class="flex items-center gap-3">
                    <div class="text-2xl font-bold text-green-500">{xactCommit}</div>
                    <div class="text-gray-300 dark:text-gray-600">/</div>
                    <div class="text-2xl font-bold text-red-500">{xactRollback}</div>
                </div>
                <div class="mt-2 text-center text-[10px] leading-tight text-gray-400">
                    <span class="font-medium text-green-500">Commits</span> (Thành công) / <span class="font-medium text-red-500">Rollbacks</span> (Bị hủy)
                </div>
            </div>
        </div>

        <div class="mb-6 flex flex-col gap-6 lg:flex-row">
            <div class="w-full rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-800 lg:w-1/3">
                <h3 class="mb-4 text-center font-medium">Trạng thái kết nối</h3>
                <div class="relative flex h-64 w-full items-center justify-center">
                    <PerformanceChart active={activeQueries} idle={idleQueries} other={otherQueries} />
                </div>
            </div>

            <div class="flex h-80 w-full flex-1 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800 lg:w-2/3">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                        <h3 class="text-sm font-medium">Kết nối trực tiếp</h3>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{refreshLabel}</span>
                </div>
                <div class="flex-1 overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">PID</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">User</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IP</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ứng dụng</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Thời gian</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            {#if activities.length === 0}
                                <tr><td colspan="6" class="py-8 text-center text-gray-400 dark:text-gray-500">Không có kết nối nào.</td></tr>
                            {:else}
                                {#each activities as act}
                                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2.5 font-mono text-xs text-gray-500 dark:text-gray-400">{act.pid}</td>
                                        <td class="px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-white">{act.usename}</td>
                                        <td class="px-3 py-2.5">
                                            {#if act.client_addr}
                                                <span class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-300">{act.client_addr}:{act.client_port ?? ''}</span>
                                            {:else}
                                                <span class="text-xs italic text-gray-400">local socket</span>
                                            {/if}
                                        </td>
                                        <td class="px-3 py-2.5">
                                            {#if act.application_name}
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{act.application_name}</span>
                                            {:else}
                                                <span class="text-gray-400">—</span>
                                            {/if}
                                        </td>
                                        <td class="px-3 py-2.5 font-mono text-xs text-gray-500 dark:text-gray-400">{formatDuration(act.duration_sec)}</td>
                                        <td class="px-3 py-2.5">
                                            {#if act.state === 'active'}
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-500/20 dark:text-green-400"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span>Active</span>
                                            {:else if act.state === 'idle'}
                                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600 dark:bg-gray-600/30 dark:text-gray-400"><span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>Idle</span>
                                            {:else}
                                                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-[10px] font-bold text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400"><span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>{act.state ?? 'unknown'}</span>
                                            {/if}
                                        </td>
                                    </tr>
                                {/each}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-6 flex w-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-sm font-medium">Truy vấn chậm</h3>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500">Top 5</span>
            </div>
            <div class="overflow-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">PID</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">User</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Thời gian</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Query</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        {#if slowQueries.length === 0}
                            <tr><td colspan="4" class="py-6 text-center text-gray-400 dark:text-gray-500">Không có truy vấn chậm nào.</td></tr>
                        {:else}
                            {#each slowQueries as sq}
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-3 py-2.5 font-mono text-xs text-gray-500 dark:text-gray-400">{sq.pid}</td>
                                    <td class="px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-white">{sq.usename}</td>
                                    <td class="px-3 py-2.5 font-mono text-xs font-medium text-orange-600 dark:text-orange-400">{formatDuration(sq.running_time_sec)}</td>
                                    <td class="px-3 py-2.5 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="max-w-2xl truncate" title={sq.query}>{sq.query}</div>
                                    </td>
                                </tr>
                            {/each}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>
