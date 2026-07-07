<script>
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import { route } from '../../lib/route.js';

    let { error = null } = $props();
</script>

<AppLayout title="DB Tracker - Config Error">
    <div class="flex h-full flex-col items-center justify-center bg-gray-50 p-6 dark:bg-gray-900">
        <div class="w-full max-w-md rounded-xl border border-red-200 bg-white p-8 text-center shadow-sm dark:border-red-500/20 dark:bg-gray-800">
            <svg class="mx-auto mb-4 h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">Chưa cấu hình hoặc bị vô hiệu hóa</h3>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                Bạn cần bật tính năng theo dõi và nhập thông tin kết nối Database trong phần
                <a href={route('settings', { section: 'database' })} class="font-medium text-accent-500 hover:underline">Cấu hình hệ thống (Settings)</a>
                để sử dụng tính năng này.
            </p>

            {#if error}
                <div class="mt-4 rounded-lg bg-red-50 p-3 text-left text-xs text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <strong>Error details:</strong><br />
                    {error}
                </div>
            {/if}
        </div>
    </div>
</AppLayout>
