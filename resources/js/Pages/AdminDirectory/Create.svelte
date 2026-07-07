<script>
    import { onMount, onDestroy } from 'svelte';
    import { useForm } from '@inertiajs/svelte';
    import flatpickr from 'flatpickr';
    import { Vietnamese } from 'flatpickr/dist/l10n/vn.js';
    import 'flatpickr/dist/flatpickr.css';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import { route } from '../../lib/route.js';

    let { roles = [] } = $props();

    const form = useForm({
        username: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        valid_from: '',
        valid_until: '',
        role: roles.length === 1 ? roles[0].id : '',
        status: 'active',
    });

    let validFromInput = $state(null);
    let validUntilInput = $state(null);
    let pickers = [];

    onMount(() => {
        const opts = { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, locale: Vietnamese };
        pickers = [
            flatpickr(validFromInput, { ...opts, onChange: ([date]) => ($form.valid_from = date ? formatDate(date) : '') }),
            flatpickr(validUntilInput, { ...opts, onChange: ([date]) => ($form.valid_until = date ? formatDate(date) : '') }),
        ];
    });

    onDestroy(() => pickers.forEach((p) => p.destroy()));

    function formatDate(date) {
        const pad = (n) => String(n).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    function submit(e) {
        e.preventDefault();
        $form.post(route('admin-directory.store'));
    }
</script>

<AppLayout title="Thêm quản trị viên">
    <div class="mx-auto max-w-5xl py-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="flex items-center gap-3 text-2xl font-bold text-gray-900 dark:text-white">
                    <div class="rounded-lg bg-accent-100 p-2 text-accent-600 dark:bg-accent-500/20 dark:text-accent-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    Thêm quản trị viên mới
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tạo tài khoản admin mới và gán quyền truy cập.</p>
            </div>
            <a href={route('admin-directory.index')} class="flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <form onsubmit={submit} class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
                    <div class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username <span class="text-red-500">*</span></label>
                                <input type="text" id="username" bind:value={$form.username} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" placeholder="user_admin" />
                                {#if $form.errors.username}<p class="mt-1 text-xs text-red-500">{$form.errors.username}</p>{/if}
                            </div>
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" id="name" bind:value={$form.name} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" placeholder="Nguyễn Văn A" />
                                {#if $form.errors.name}<p class="mt-1 text-xs text-red-500">{$form.errors.name}</p>{/if}
                            </div>
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                    </div>
                                    <input type="email" id="email" bind:value={$form.email} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" placeholder="admin@domain.com" />
                                </div>
                                {#if $form.errors.email}<p class="mt-1 text-xs text-red-500">{$form.errors.email}</p>{/if}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 border-t border-gray-100 pt-4 dark:border-gray-700 md:grid-cols-2">
                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mật khẩu <span class="text-red-500">*</span></label>
                                <input type="password" id="password" bind:value={$form.password} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="••••••••" />
                                {#if $form.errors.password}<p class="mt-1 text-xs text-red-500">{$form.errors.password}</p>{/if}
                            </div>
                            <div class="space-y-2">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                                <input type="password" id="password_confirmation" bind:value={$form.password_confirmation} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="••••••••" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 border-t border-gray-100 pt-4 dark:border-gray-700 md:grid-cols-2">
                            <div class="space-y-2">
                                <label for="valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Có hiệu lực từ ngày</label>
                                <input type="text" id="valid_from" bind:this={validFromInput} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Chọn ngày giờ bắt đầu..." />
                                <p class="text-xs text-gray-500">Bỏ trống nếu có hiệu lực ngay lập tức.</p>
                                {#if $form.errors.valid_from}<p class="mt-1 text-xs text-red-500">{$form.errors.valid_from}</p>{/if}
                            </div>
                            <div class="space-y-2">
                                <label for="valid_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ngày hết hạn</label>
                                <input type="text" id="valid_until" bind:this={validUntilInput} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Chọn ngày giờ hết hạn..." />
                                <p class="text-xs text-gray-500">Bỏ trống nếu không bao giờ hết hạn.</p>
                                {#if $form.errors.valid_until}<p class="mt-1 text-xs text-red-500">{$form.errors.valid_until}</p>{/if}
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 dark:border-gray-700">
                            <div class="mb-3 flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gán Quyền (Role) <span class="text-red-500">*</span></label>
                                <a href="{route('settings')}?section=permissions" class="text-xs text-accent-600 hover:underline dark:text-accent-400">Quản lý Rules (Roles)</a>
                            </div>

                            {#if roles.length === 0}
                                <div class="rounded-lg border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-800/30 dark:bg-yellow-900/20 dark:text-yellow-300">
                                    Chưa có Role nào trong hệ thống. Vui lòng sang mục Quản lý Rules để tạo Role trước.
                                </div>
                            {:else}
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    {#each roles as role}
                                        <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm transition-all duration-200 {$form.role === role.id ? 'border-accent-500 bg-accent-50 ring-1 ring-accent-500 dark:border-accent-500 dark:bg-accent-900/20' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800'}">
                                            <input type="radio" name="role" value={role.id} bind:group={$form.role} class="sr-only" />
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="mb-1 flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
                                                        {role.name}
                                                        {#if role.is_system}
                                                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[9px] font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-400">SYSTEM</span>
                                                        {/if}
                                                    </span>
                                                    <span class="mt-1 flex items-center text-xs text-gray-500">{role.description || 'Không có mô tả.'}</span>
                                                </span>
                                            </span>
                                            <svg class="h-5 w-5 text-accent-600 transition-opacity duration-200 {$form.role === role.id ? 'opacity-100' : 'opacity-0'}" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                            </svg>
                                        </label>
                                    {/each}
                                </div>
                            {/if}
                            {#if $form.errors.role}<p class="mt-1 text-xs text-red-500">{$form.errors.role}</p>{/if}
                        </div>

                        <div class="border-t border-gray-100 pt-4 dark:border-gray-700">
                            <label class="flex cursor-pointer items-center gap-3">
                                <div class="relative">
                                    <input type="checkbox" class="peer sr-only" checked={$form.status === 'active'} onchange={(e) => ($form.status = e.target.checked ? 'active' : '')} />
                                    <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:top-[2px] after:start-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-accent-600 peer-checked:after:translate-x-full peer-focus:outline-none dark:border-gray-600 dark:bg-gray-700"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                    Trạng thái: <strong class="ml-1 text-green-600">Hoạt động</strong>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <a href={route('admin-directory.index')} class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Huỷ</a>
                        <button type="submit" disabled={$form.processing} class="flex items-center gap-2 rounded-lg bg-accent-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent-700 disabled:opacity-60">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Lưu
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-5 text-blue-800 dark:border-blue-800/30 dark:bg-blue-900/20 dark:text-blue-300">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h4 class="mb-1 font-bold">Quy định Bảo mật</h4>
                            <p class="text-sm leading-relaxed text-blue-700 dark:text-blue-400">
                                Hệ thống yêu cầu mật khẩu phải đạt chuẩn: tối thiểu 8 ký tự, bao gồm chữ hoa, chữ thường và số. Các tài khoản Super Admin bắt buộc phải được theo dõi (Audit Log) mọi hành động.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800">
                    <h4 class="mb-4 font-bold text-gray-900 dark:text-white">Các cấp độ Phân Quyền</h4>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="mt-1 shrink-0 rounded-lg bg-red-100 p-1.5 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-sm text-gray-900 dark:text-white">Super Admin</strong>
                                <span class="mt-1 block text-xs leading-relaxed text-gray-500">Nên giới hạn số lượng tài khoản này. Có thể can thiệp sâu vào Database, Source Code và Cấu hình Server.</span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-1 shrink-0 rounded-lg bg-green-100 p-1.5 text-green-600 dark:bg-green-500/20 dark:text-green-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-sm text-gray-900 dark:text-white">Support / CSKH</strong>
                                <span class="mt-1 block text-xs leading-relaxed text-gray-500">Chỉ sử dụng cho đội ngũ chăm sóc khách hàng. Được quyền tra cứu thông tin nhưng không thể lưu các thay đổi hệ thống.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AppLayout>
