<script>
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import PageHeader from '../../Components/DbTracker/PageHeader.svelte';

    let { creds, roles = [] } = $props();

    function setupDefense() {
        alert('Tính năng phòng thủ tự động đang được phát triển!');
    }
</script>

{#snippet check(ok)}
    {#if ok}
        <span class="inline-flex items-center justify-center rounded-full bg-green-100 p-1 text-green-600 dark:bg-green-500/20 dark:text-green-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
    {:else}
        <span class="text-gray-300 dark:text-gray-600">-</span>
    {/if}
{/snippet}

<AppLayout title="DB Tracker - Security & Users">
    <div class="flex h-full flex-col bg-gray-50 p-6 dark:bg-gray-900">
        <PageHeader title="Security & Users" {creds} />

        <div class="grid min-h-0 flex-1 grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800 xl:col-span-2">
                <div class="border-b border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <h3 class="font-medium">Database Roles &amp; Users</h3>
                </div>
                <div class="flex-1 overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tên vai trò</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Superuser</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Create Role</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Create DB</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Can Login</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            {#each roles as role}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{role.role_name}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">{@render check(role.rolsuper)}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">{@render check(role.rolcreaterole)}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">{@render check(role.rolcreatedb)}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">{@render check(role.rolcanlogin)}</td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col gap-6 overflow-y-auto">
                <div class="relative rounded-xl border border-red-200 bg-white p-5 shadow-sm dark:border-red-500/20 dark:bg-gray-800">
                    <div class="mb-4 flex items-center gap-3 text-red-600 dark:text-red-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h3 class="text-lg font-bold">Cảnh báo bảo mật</h3>
                    </div>
                    <p class="mb-5 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                        Rà soát định kỳ các vai trò có quyền cao để giảm thiểu rủi ro truy cập trái phép.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5 shrink-0 rounded-lg bg-red-100 p-1.5 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-sm text-gray-900 dark:text-white">Hạn chế Superuser</strong>
                                <span class="text-xs leading-relaxed text-gray-500">Chỉ cấp quyền superuser cho tài khoản quản trị hạ tầng thực sự cần thiết.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5 shrink-0 rounded-lg bg-red-100 p-1.5 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-sm text-gray-900 dark:text-white">Xoay vòng mật khẩu</strong>
                                <span class="text-xs leading-relaxed text-gray-500">Đặt hạn dùng mật khẩu và yêu cầu đổi định kỳ cho các role đăng nhập được.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5 shrink-0 rounded-lg bg-red-100 p-1.5 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-sm text-gray-900 dark:text-white">Kiểm tra Audit Log</strong>
                                <span class="text-xs leading-relaxed text-gray-500">Theo dõi thường xuyên tab Data &amp; Activity để phát hiện thay đổi bất thường.</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="rounded-xl border-0 bg-gradient-to-br from-indigo-500 to-purple-600 p-5 text-white shadow-lg">
                    <h3 class="mb-2 flex items-center gap-2 text-lg font-bold">
                        <svg class="h-5 w-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                        Chủ động phòng thủ
                    </h3>
                    <p class="mb-5 text-sm leading-relaxed text-white/80">
                        Tự động khoá hoặc cảnh báo khi phát hiện hành vi truy cập bất thường tới CSDL.
                    </p>
                    <button onclick={setupDefense} class="w-full rounded-lg bg-white py-2.5 text-sm font-bold text-indigo-600 shadow-sm transition-colors hover:bg-indigo-50">
                        Thiết lập phòng thủ
                    </button>
                </div>
            </div>
        </div>
    </div>
</AppLayout>
