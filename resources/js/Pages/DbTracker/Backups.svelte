<script>
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import PageHeader from '../../Components/DbTracker/PageHeader.svelte';

    let { creds, snapshots = [] } = $props();

    function createBackup() {
        alert('Tính năng tạo backup theo yêu cầu đang được phát triển!');
    }

    function restoreBackup() {
        alert('Tính năng khôi phục từ backup đang được phát triển!');
    }
</script>

<AppLayout title="DB Tracker - Backups & Recovery">
    <div class="flex h-full flex-col bg-gray-50 p-6 dark:bg-gray-900">
        <PageHeader title="Backups & Recovery" {creds}>
            {#snippet children()}
                <button onclick={createBackup} class="flex items-center gap-2 rounded-lg bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Tạo backup mới
                </button>
            {/snippet}
        </PageHeader>

        <div class="mb-6 flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                <h3 class="flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                    <svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.96 2.06c-.63 0-1.25.16-1.8.46L4.72 5.67A3.67 3.67 0 002.85 8.9v6.2c0 1.34.73 2.57 1.87 3.23l5.44 3.15c1.1.64 2.45.64 3.55 0l5.44-3.15c1.14-.66 1.87-1.89 1.87-3.23V8.9c0-1.34-.73-2.57-1.87-3.23l-5.44-3.15a3.67 3.67 0 00-1.75-.46zm0 1.48c.36 0 .72.1.1.02.26l5.45 3.15c.6.35.98 1.02.98 1.71v6.2c0 .69-.38 1.36-.98 1.71l-5.45 3.15c-.6.35-1.37.35-1.97 0L4.54 16.3a1.96 1.96 0 01-.98-1.71V8.4c0-.69.38-1.36.98-1.71l5.45-3.15c.3-.18.64-.26.97-.26zM12 7.02c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5zm0 1.5c1.94 0 3.5 1.56 3.5 3.5S13.94 15.5 12 15.5 8.5 13.94 8.5 12 10.06 8.52 12 8.52z"></path></svg>
                    AWS RDS Snapshots
                </h3>
                <span class="rounded-full border border-gray-200 bg-gray-100 px-3 py-1 text-xs text-gray-600 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-300">ap-southeast-1</span>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="sticky top-0 z-10 bg-gray-50 backdrop-blur-sm dark:bg-gray-900/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Snapshot ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Loại</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Thời gian tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Dung lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Trạng thái</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        {#each snapshots as snap}
                            <tr class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="flex items-center gap-2 whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                    <svg class="h-4 w-4 text-gray-400 transition-colors group-hover:text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    {snap.id}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {#if snap.type === 'Automated'}
                                        <span class="rounded bg-blue-100 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">Tự động</span>
                                    {:else}
                                        <span class="rounded bg-purple-100 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-purple-700 dark:bg-purple-500/20 dark:text-purple-400">Thủ công</span>
                                    {/if}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-600 dark:text-gray-300">{snap.created_at}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{snap.size}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1.5 font-medium"><span class="h-2 w-2 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></span> Sẵn sàng</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <button onclick={restoreBackup} class="flex w-full items-center justify-end gap-1.5 text-accent-600 transition-colors hover:text-accent-800 dark:text-accent-400 dark:hover:text-accent-300">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Khôi phục
                                    </button>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid shrink-0 grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="group rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-indigo-500/10 dark:from-indigo-900/20 dark:to-gray-800">
                <h3 class="mb-2 flex items-center gap-2 text-lg font-bold text-indigo-800 dark:text-indigo-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Point-in-time Recovery
                </h3>
                <p class="mb-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                    Cho phép khôi phục CSDL về bất kỳ thời điểm nào trong vòng thời gian lưu trữ backup.
                </p>
            </div>

            <div class="group rounded-xl border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-amber-500/10 dark:from-amber-900/20 dark:to-gray-800">
                <h3 class="mb-2 flex items-center gap-2 text-lg font-bold text-amber-800 dark:text-amber-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    S3 Backups
                </h3>
                <p class="mb-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                    Bản backup logic (pg_dump) được lưu trữ định kỳ trên S3, tách biệt khỏi RDS snapshot.
                </p>
            </div>
        </div>
    </div>
</AppLayout>
