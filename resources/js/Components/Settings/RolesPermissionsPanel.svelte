<script>
    import { fly, fade } from 'svelte/transition';
    import { router, useForm } from '@inertiajs/svelte';
    import { route } from '../../lib/route.js';

    let { rolesData = [], categories = [], globalActions = [], hasTables = true } = $props();

    const ACTION_LABELS = { view: 'Xem', create: 'Tạo', edit: 'Sửa', delete: 'Xoá', publish: 'Xuất bản' };

    let showModal = $state(false);
    let isEdit = $state(false);

    const form = useForm({ id: '', name: '', description: '', permissions: {}, is_edit: '0' });

    function openAdd() {
        isEdit = false;
        $form.reset();
        $form.permissions = {};
        $form.is_edit = '0';
        showModal = true;
    }

    function openEdit(roleId) {
        isEdit = true;
        const role = rolesData.find((r) => r.id === roleId);
        $form.id = role.id;
        $form.name = role.name;
        $form.description = role.description ?? '';
        $form.permissions = JSON.parse(JSON.stringify(role.perms ?? {}));
        $form.is_edit = '1';
        showModal = true;
    }

    function togglePerm(categoryId, action) {
        const current = $form.permissions[categoryId] ?? {};
        $form.permissions = { ...$form.permissions, [categoryId]: { ...current, [action]: !current[action] } };
    }

    function submit(e) {
        e.preventDefault();
        $form.post(route('roles.store'), {
            onSuccess: () => { showModal = false; },
        });
    }

    function destroyRole(roleId) {
        if (confirm('Bạn có chắc chắn muốn xoá vai trò này?')) {
            router.delete(route('roles.destroy', roleId));
        }
    }
</script>

<div class="w-full">
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">System Roles</h2>
            <p class="mt-1 text-sm text-gray-500">Quản lý vai trò và ma trận phân quyền theo module.</p>
        </div>
        <button type="button" onclick={openAdd} class="inline-flex items-center gap-2 rounded-lg bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm vai trò mới
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        {#if !hasTables}
            <div class="p-8 text-center text-gray-500">Chưa khởi tạo bảng phân quyền. Vui lòng chạy migration/seeder.</div>
        {:else if rolesData.length === 0}
            <div class="flex flex-col items-center p-8 text-center text-gray-500">
                <svg class="mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <p>Chưa có vai trò nào được tạo.</p>
            </div>
        {:else}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-700 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tên vai trò</th>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Mô tả</th>
                            <th class="px-6 py-4 text-right font-semibold">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        {#each rolesData as role}
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 dark:text-gray-200">{role.name}</span>
                                        {#if role.is_system}
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-400">SYSTEM</span>
                                        {/if}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500">{role.id}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{role.description || '-'}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {#if !role.is_system}
                                            <button type="button" onclick={() => openEdit(role.id)} class="inline-flex items-center gap-1.5 rounded-md bg-accent-50 px-3 py-1.5 text-xs font-semibold text-accent-600 transition-colors hover:bg-accent-100 hover:text-accent-700 dark:bg-accent-500/10 dark:text-accent-400 dark:hover:bg-accent-500/20">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Sửa quyền
                                            </button>
                                            <button type="button" onclick={() => destroyRole(role.id)} class="inline-flex items-center gap-1.5 rounded-md bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-100 hover:text-red-700 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Xoá
                                            </button>
                                        {:else}
                                            <span class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-md bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-400 dark:bg-gray-800/50">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Full Access
                                            </span>
                                        {/if}
                                    </div>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {/if}
    </div>

    {#if showModal}
        <div class="relative z-50" role="dialog" aria-modal="true">
            <div transition:fade={{ duration: 200 }} class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" onclick={() => (showModal = false)}></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div transition:fly={{ x: 400, duration: 300 }} class="pointer-events-auto w-screen max-w-[800px]">
                        <form onsubmit={submit} class="flex h-full flex-col divide-y divide-gray-200 bg-gray-50 shadow-xl dark:divide-gray-800 dark:bg-gray-900">
                            <div class="h-0 flex-1 overflow-y-auto">
                                <div class="relative overflow-hidden bg-accent-600 px-4 py-6 dark:bg-gray-800 sm:px-6">
                                    <div class="relative z-10 flex items-center justify-between">
                                        <h2 class="text-xl font-bold text-white">{isEdit ? `Sửa vai trò: ${$form.name}` : 'Tạo vai trò mới'}</h2>
                                        <button type="button" onclick={() => (showModal = false)} class="relative rounded-md text-accent-200 hover:text-white focus:outline-none">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <p class="relative z-10 mt-2 text-sm text-accent-100 dark:text-gray-400">{isEdit ? 'Cập nhật thông tin và quyền của vai trò.' : 'Nhập thông tin và chọn quyền cho vai trò mới.'}</p>
                                </div>

                                <div class="px-4 sm:px-6">
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-6 border-b border-gray-200 py-6 dark:border-gray-800">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-300">Role ID <span class="text-red-500">*</span></label>
                                            <input type="text" bind:value={$form.id} readonly={isEdit} required placeholder="Ví dụ: content_manager"
                                                class="mt-1 block w-full rounded-md border-0 px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-accent-600 dark:text-white {isEdit ? 'cursor-not-allowed bg-gray-100 dark:bg-gray-800' : 'dark:bg-gray-900'}" />
                                            {#if isEdit}<p class="mt-1 text-[11px] text-gray-400">Không thể đổi Role ID sau khi tạo.</p>{/if}
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-300">Tên vai trò <span class="text-red-500">*</span></label>
                                            <input type="text" bind:value={$form.name} required placeholder="Ví dụ: Content Manager"
                                                class="mt-1 block w-full rounded-md border-0 px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-accent-600 dark:bg-gray-900 dark:text-white dark:ring-gray-700" />
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-300">Mô tả</label>
                                            <textarea bind:value={$form.description} rows="2"
                                                class="mt-1 block w-full rounded-md border-0 px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-accent-600 dark:bg-gray-900 dark:text-white dark:ring-gray-700"></textarea>
                                        </div>
                                    </div>

                                    <div class="py-6">
                                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                                            <svg class="h-4 w-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            Ma trận phân quyền
                                        </h3>

                                        <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-800/80">
                                                    <tr>
                                                        <th class="w-1/3 px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Module / Tài nguyên</th>
                                                        {#each globalActions as action}
                                                            <th class="border-l border-gray-200 px-2 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                                                {ACTION_LABELS[action] ?? action}
                                                            </th>
                                                        {/each}
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 bg-gray-50 dark:divide-gray-700 dark:bg-gray-900">
                                                    {#each categories as category}
                                                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                                            <td class="px-5 py-3 text-sm font-bold text-gray-900 dark:text-gray-200" style="padding-left: {category.computed_level > 1 ? category.computed_level * 1.5 + 1 : 1.25}rem;">
                                                                {#if category.computed_level > 1}<span class="mr-1 font-normal text-gray-300 dark:text-gray-600">↳</span>{/if}
                                                                <span class={category.computed_level > 1 ? 'font-medium text-gray-600 dark:text-gray-400' : 'font-bold'}>{category.name}</span>
                                                            </td>
                                                            {#each globalActions as action}
                                                                <td class="border-l border-gray-100 px-2 py-3.5 text-center dark:border-gray-800">
                                                                    <label class="inline-flex h-full w-full items-center justify-center">
                                                                        <input
                                                                            type="checkbox"
                                                                            checked={Boolean($form.permissions[category.id]?.[action])}
                                                                            onchange={() => togglePerm(category.id, action)}
                                                                            class="h-4 w-4 cursor-pointer rounded border-gray-300 bg-gray-100 text-accent-600 transition-all hover:scale-110 focus:ring-2 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700"
                                                                        />
                                                                    </label>
                                                                </td>
                                                            {/each}
                                                        </tr>
                                                    {/each}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800">
                                <button type="button" onclick={() => (showModal = false)} class="rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 transition-colors hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-700">Huỷ</button>
                                <button type="submit" disabled={$form.processing} class="inline-flex justify-center rounded-lg bg-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-accent-500 focus:ring-2 focus:ring-accent-600 focus:ring-offset-2 disabled:opacity-60">
                                    {isEdit ? 'Lưu thay đổi' : 'Tạo vai trò'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>
