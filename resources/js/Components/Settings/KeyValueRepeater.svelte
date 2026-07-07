<script>
    let { rows = [], columns = { key: 'Key', value: 'Value' }, disabled = false, onChange } = $props();

    const colEntries = $derived(Object.entries(columns));
    const gridCols = $derived(Math.min(colEntries.length, 3));

    function update(index, colKey, val) {
        onChange(rows.map((row, i) => (i === index ? { ...row, [colKey]: val } : row)));
    }

    function addRow() {
        const blank = Object.fromEntries(colEntries.map(([key]) => [key, '']));
        onChange([...rows, blank]);
    }

    function removeRow(index) {
        onChange(rows.filter((_, i) => i !== index));
    }
</script>

<div class="w-full">
    <div class="space-y-3">
        {#each rows as row, index}
            <div class="flex items-start gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="grid flex-1 gap-4" style="grid-template-columns: repeat({gridCols}, minmax(0, 1fr));">
                    {#each colEntries as [colKey, colLabel]}
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">{colLabel}</label>
                            <input
                                type="text"
                                value={row[colKey] ?? ''}
                                {disabled}
                                oninput={(e) => update(index, colKey, e.target.value)}
                                class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition-colors focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            />
                        </div>
                    {/each}
                </div>
                <button type="button" onclick={() => removeRow(index)} {disabled} title="Xoá dòng này" class="mt-6 rounded-md bg-red-50 p-2 text-red-500 transition-colors hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        {/each}
    </div>

    {#if rows.length === 0}
        <div class="rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800/30">
            <svg class="mx-auto mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Chưa có dữ liệu nào được cấu hình.</p>
        </div>
    {/if}

    <button type="button" onclick={addRow} {disabled} class="mt-4 inline-flex items-center gap-2 rounded-lg border border-accent-200 bg-accent-50 px-4 py-2 text-sm font-semibold text-accent-600 transition-colors hover:bg-accent-100 dark:border-accent-500/30 dark:bg-accent-500/10 dark:text-accent-300 dark:hover:bg-accent-500/20">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Thêm dòng mới
    </button>
</div>
