<script>
    import { useForm } from '@inertiajs/svelte';
    import SettingsField from './SettingsField.svelte';
    import { route } from '../../lib/route.js';

    let { structure, section, scopeParam, scope, values, useSystem } = $props();

    const groups = $derived(Object.entries(structure[section].groups));

    function fieldPath(groupId, fieldId) {
        return `${section}/${groupId}/${fieldId}`;
    }

    function initialValue(path, field) {
        const raw = values[path] ?? '';
        if (field.type === 'multiselect') return raw ? String(raw).split(',') : [];
        if (field.type === 'repeater') {
            try {
                const parsed = JSON.parse(raw || '[]');
                return Array.isArray(parsed) ? parsed : [];
            } catch {
                return [];
            }
        }
        return raw;
    }

    function buildInitialConfig() {
        const config = {};
        for (const [groupId, group] of groups) {
            for (const [fieldId, field] of Object.entries(group.fields)) {
                const path = fieldPath(groupId, fieldId);
                config[path] = initialValue(path, field);
            }
        }
        return config;
    }

    const form = useForm({
        section,
        scope_param: scopeParam,
        config: buildInitialConfig(),
    });

    let useSystemState = $state({ ...useSystem });
    let openGroups = $state(Object.fromEntries(groups.map(([groupId], i) => [groupId, i === 0])));
    let productErrors = $state({});

    function toggleUseSystem(path, checked) {
        useSystemState = { ...useSystemState, [path]: checked };
    }

    function validateProductRows(rows) {
        const errors = {};
        rows.forEach((row, i) => {
            if (!row.name?.trim()) errors[`${i}_name`] = true;
            if (!row.host?.trim()) errors[`${i}_host`] = true;
            if (!String(row.port ?? '').trim()) errors[`${i}_port`] = true;
            if (!row.username?.trim()) errors[`${i}_username`] = true;
            const auth = row.auth_type || 'password';
            if (auth === 'password' && !row.password?.trim()) errors[`${i}_password`] = true;
            if ((auth === 'key_text' || auth === 'pem_file') && !row.private_key?.trim()) errors[`${i}_private_key`] = true;
        });
        return errors;
    }

    function submit(e) {
        e.preventDefault();

        const productsPath = 'software_connections/products/list';
        if ($form.config[productsPath]) {
            const errors = validateProductRows($form.config[productsPath]);
            productErrors = errors;
            if (Object.keys(errors).length > 0) return;
        }

        $form.transform((data) => ({
            ...data,
            use_system: Object.fromEntries(Object.entries(useSystemState).filter(([, v]) => v)),
        })).post(route('settings.save'));
    }
</script>

<form onsubmit={submit} class="space-y-5">
    {#each groups as [groupId, group]}
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <button
                type="button"
                onclick={() => (openGroups[groupId] = !openGroups[groupId])}
                class="flex w-full items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-3.5 transition-colors hover:bg-gray-100 focus:outline-none dark:border-gray-800 dark:bg-gray-800/50 dark:hover:bg-gray-800/80"
            >
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{group.label}</h3>
                <svg class="h-5 w-5 text-gray-500 transition-transform duration-200 {openGroups[groupId] ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            {#if openGroups[groupId]}
                <div class="space-y-2 p-4 sm:p-6">
                    {#each Object.entries(group.fields) as [fieldId, field]}
                        {@const path = fieldPath(groupId, fieldId)}
                        {@const isSystem = Boolean(useSystemState[path])}
                        {@const disabled = scope !== 'default' && isSystem}
                        <div class="flex flex-col items-start gap-2 border-b border-dashed border-gray-200 py-4 last:border-0 last:pb-0 dark:border-gray-800 lg:flex-row lg:gap-6">
                            <div class="flex w-full flex-col justify-start lg:w-1/3 lg:pt-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{field.label}</label>
                                <span class="mt-1 inline-block w-max rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] uppercase tracking-widest text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                    [{scope === 'default' ? 'Global' : scope === 'websites' ? 'Website' : 'Store'}]
                                </span>
                            </div>

                            <div class="flex w-full flex-col items-start gap-4 sm:flex-row lg:w-2/3">
                                <div class="min-w-0 w-full sm:flex-1">
                                    <SettingsField
                                        {path}
                                        {field}
                                        value={$form.config[path]}
                                        {disabled}
                                        errors={productErrors}
                                        onInput={(val) => ($form.config[path] = val)}
                                    />
                                </div>

                                {#if scope !== 'default'}
                                    <div class="flex flex-shrink-0 items-center gap-2 pt-2 sm:py-2 sm:pt-0">
                                        <input
                                            type="checkbox"
                                            checked={isSystem}
                                            onchange={(e) => toggleUseSystem(path, e.target.checked)}
                                            class="h-4 w-4 cursor-pointer rounded border-gray-300 text-accent-600 focus:ring-accent-500"
                                        />
                                        <label class="cursor-pointer select-none whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">Dùng giá trị hệ thống</label>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {/each}
                </div>
            {/if}
        </div>
    {/each}

    <div class="sticky bottom-6 mt-8 flex justify-end">
        <button
            type="submit"
            disabled={$form.processing}
            class="inline-flex items-center gap-2 rounded-lg bg-accent-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-accent-500/30 transition-all hover:-translate-y-0.5 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 disabled:opacity-60"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Lưu cấu hình
        </button>
    </div>
</form>
