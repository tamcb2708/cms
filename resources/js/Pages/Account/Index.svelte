<script>
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import { route } from '../../lib/route.js';

    let { user } = $props();

    const isAdminEios = user.is_root_admin;

    function formatDate(value) {
        if (!value) return null;
        const d = new Date(value);
        return d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    const profileForm = useForm({
        name: user.name ?? '',
        email: user.email ?? '',
        phone: user.phone ?? '',
        department: user.department ?? '',
        job_title: user.job_title ?? '',
        locale: user.locale ?? 'vi',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    function submitProfile(e) {
        e.preventDefault();
        $profileForm.post(route('account.profile'));
    }

    function submitPassword(e) {
        e.preventDefault();
        $passwordForm.post(route('account.password'), {
            onSuccess: () => $passwordForm.reset('current_password', 'password', 'password_confirmation'),
        });
    }
</script>

<AppLayout title="Tài khoản của tôi">
    <div class="mx-auto max-w-[1000px] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="flex items-center gap-2 text-2xl font-bold text-gray-900 dark:text-white">
                <svg class="h-6 w-6 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Thông tin tài khoản
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Quản lý thông tin cá nhân và bảo mật tài khoản của bạn.</p>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div class="space-y-6 md:col-span-2">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-white/[0.02]">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Chi tiết tài khoản</h3>
                    </div>
                    <div class="p-6">
                        <form onsubmit={submitProfile} class="space-y-5">
                            <div class="mb-5 grid grid-cols-1 gap-5 rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Username</label>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{user.username || '-'}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Role (Quyền hạn)</label>
                                    <div class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {user.cms_role ? user.cms_role.name : 'N/A'}
                                        {#if user.cms_role?.is_system}
                                            <span class="rounded bg-accent-100 px-1.5 py-0.5 text-[10px] font-bold text-accent-700 dark:bg-accent-500/20 dark:text-accent-400">SYSTEM</span>
                                        {/if}
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Có hiệu lực từ</label>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{formatDate(user.valid_from) ?? 'Ngay lập tức'}</div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ngày hết hạn</label>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{formatDate(user.valid_until) ?? 'Không bao giờ'}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Họ và tên</label>
                                    <input type="text" bind:value={$profileForm.name} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                                    {#if $profileForm.errors.name}<p class="mt-1 text-xs text-red-500">{$profileForm.errors.name}</p>{/if}
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" bind:value={$profileForm.email} readonly={isAdminEios} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white {isAdminEios ? 'cursor-not-allowed bg-gray-50 opacity-60' : ''}" />
                                    {#if $profileForm.errors.email}<p class="mt-1 text-xs text-red-500">{$profileForm.errors.email}</p>{/if}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Điện thoại</label>
                                    <input type="text" bind:value={$profileForm.phone} readonly={isAdminEios} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white {isAdminEios ? 'cursor-not-allowed bg-gray-50 opacity-60' : ''}" />
                                    {#if $profileForm.errors.phone}<p class="mt-1 text-xs text-red-500">{$profileForm.errors.phone}</p>{/if}
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phòng ban</label>
                                    <input type="text" bind:value={$profileForm.department} readonly={isAdminEios} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white {isAdminEios ? 'cursor-not-allowed bg-gray-50 opacity-60' : ''}" />
                                    {#if $profileForm.errors.department}<p class="mt-1 text-xs text-red-500">{$profileForm.errors.department}</p>{/if}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Chức danh</label>
                                    <input type="text" bind:value={$profileForm.job_title} readonly={isAdminEios} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white {isAdminEios ? 'cursor-not-allowed bg-gray-50 opacity-60' : ''}" />
                                    {#if $profileForm.errors.job_title}<p class="mt-1 text-xs text-red-500">{$profileForm.errors.job_title}</p>{/if}
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ngôn ngữ</label>
                                    {#if isAdminEios}
                                        <input type="text" readonly value={user.locale === 'vi' ? 'Tiếng Việt' : 'English'} class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 opacity-60 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                                    {:else}
                                        <select bind:value={$profileForm.locale} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                            <option value="en">English</option>
                                            <option value="vi">Tiếng Việt</option>
                                        </select>
                                        {#if $profileForm.errors.locale}<p class="mt-1 text-xs text-red-500">{$profileForm.errors.locale}</p>{/if}
                                    {/if}
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" disabled={$profileForm.processing} class="inline-flex items-center gap-2 rounded-lg bg-accent-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-accent-700 focus:outline-none disabled:opacity-60">
                                    Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {#if !isAdminEios}
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-white/[0.02]">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Đổi mật khẩu</h3>
                        </div>
                        <div class="p-6">
                            <form onsubmit={submitPassword} class="space-y-5">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Mật khẩu hiện tại</label>
                                    <input type="password" bind:value={$passwordForm.current_password} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                                    {#if $passwordForm.errors.current_password}<p class="mt-1 text-xs text-red-500">{$passwordForm.errors.current_password}</p>{/if}
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Mật khẩu mới</label>
                                    <input type="password" bind:value={$passwordForm.password} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                                    {#if $passwordForm.errors.password}<p class="mt-1 text-xs text-red-500">{$passwordForm.errors.password}</p>{/if}
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Xác nhận mật khẩu</label>
                                    <input type="password" bind:value={$passwordForm.password_confirmation} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                                </div>
                                <div class="pt-2">
                                    <button type="submit" disabled={$passwordForm.processing} class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none disabled:opacity-60 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                        Cập nhật mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                {:else}
                    <div class="flex h-full flex-col items-center justify-center rounded-xl border border-gray-200 bg-gray-50 p-6 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900/50">
                        <svg class="mb-3 h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <h3 class="mb-1 text-sm font-bold text-gray-900 dark:text-white">Mật khẩu được bảo vệ</h3>
                        <p class="text-xs text-gray-500">Tài khoản này là tài khoản Root System nên không được phép đổi mật khẩu từ giao diện người dùng.</p>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</AppLayout>
