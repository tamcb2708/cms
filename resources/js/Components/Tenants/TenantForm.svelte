<script>
    import { route } from '../../lib/route.js';

    let { form, mode = 'create', tenantId = null } = $props();

    function submit(e) {
        e.preventDefault();
        if (mode === 'edit') {
            $form.put(route('tenants.update', tenantId));
        } else {
            $form.post(route('tenants.store'));
        }
    }
</script>

<form onsubmit={submit} class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
    <div class="space-y-6 p-6">
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tên <span class="text-red-500">*</span></label>
            <input type="text" id="name" bind:value={$form.name} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Acme Corp" />
            {#if $form.errors.name}<p class="mt-1 text-xs text-red-500">{$form.errors.name}</p>{/if}
        </div>

        <div class="space-y-2">
            <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain <span class="text-red-500">*</span></label>
            <input type="text" id="domain" bind:value={$form.domain} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="acme.example.com" />
            {#if $form.errors.domain}<p class="mt-1 text-xs text-red-500">{$form.errors.domain}</p>{/if}
        </div>

        <div class="space-y-2">
            <label for="database_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Database</label>
            <input type="text" id="database_name" bind:value={$form.database_name} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="tenant_acme" />
            {#if $form.errors.database_name}<p class="mt-1 text-xs text-red-500">{$form.errors.database_name}</p>{/if}
        </div>

        <div class="space-y-2">
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Trạng thái <span class="text-red-500">*</span></label>
            <select id="status" bind:value={$form.status} class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="suspended">Suspended</option>
            </select>
            {#if $form.errors.status}<p class="mt-1 text-xs text-red-500">{$form.errors.status}</p>{/if}
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
        <a href={route('tenants.index')} class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Huỷ</a>
        <button type="submit" disabled={$form.processing} class="flex items-center gap-2 rounded-lg bg-accent-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent-700 disabled:opacity-60">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Lưu
        </button>
    </div>
</form>
