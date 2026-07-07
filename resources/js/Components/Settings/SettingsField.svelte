<script>
    import ProductConnectionsRepeater from './ProductConnectionsRepeater.svelte';
    import KeyValueRepeater from './KeyValueRepeater.svelte';

    let { path, field, value, disabled, onInput, errors = {} } = $props();

    const scopeArr = $derived(Array.isArray(value) ? value : String(value ?? '').split(','));

    function selectedOptions(event) {
        return Array.from(event.target.selectedOptions).map((o) => o.value);
    }
</script>

{#if field.type === 'select'}
    <select
        value={value ?? ''}
        {disabled}
        onchange={(e) => onInput(e.target.value)}
        class="w-full max-w-md rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:text-white {disabled ? 'cursor-not-allowed bg-gray-100 opacity-60 dark:bg-gray-800' : 'bg-white dark:bg-gray-800'}"
    >
        {#each Object.entries(field.options) as [optVal, optLabel]}
            <option value={optVal}>{optLabel}</option>
        {/each}
    </select>
{:else if field.type === 'multiselect'}
    <select
        multiple
        size="5"
        {disabled}
        onchange={(e) => onInput(selectedOptions(e))}
        class="custom-scrollbar w-full max-w-md rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:text-white {disabled ? 'cursor-not-allowed bg-gray-100 opacity-60 dark:bg-gray-800' : 'bg-white dark:bg-gray-800'}"
    >
        {#each Object.entries(field.options) as [optVal, optLabel]}
            <option value={optVal} selected={scopeArr.includes(optVal)}>{optLabel}</option>
        {/each}
    </select>
    <p class="mt-1 text-[11px] text-gray-500">Giữ Ctrl/Cmd để chọn nhiều giá trị.</p>
{:else if field.type === 'color'}
    <div class="flex items-center gap-2">
        <input type="color" value={value || '#000000'} {disabled} oninput={(e) => onInput(e.target.value)} class="h-9 w-14 cursor-pointer rounded border border-gray-300 p-0.5 dark:border-gray-700" />
        <input type="text" value={value ?? ''} readonly class="w-24 rounded-md border border-gray-300 bg-gray-50 px-2 py-1.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800" />
    </div>
{:else if field.type === 'repeater'}
    {#if path === 'software_connections/products/list'}
        <ProductConnectionsRepeater rows={Array.isArray(value) ? value : []} {disabled} onChange={onInput} {errors} />
    {:else}
        <KeyValueRepeater rows={Array.isArray(value) ? value : []} columns={field.columns ?? { key: 'Key', value: 'Value' }} {disabled} onChange={onInput} />
    {/if}
{:else}
    <input
        type={field.type}
        value={value ?? ''}
        {disabled}
        oninput={(e) => onInput(e.target.value)}
        class="w-full max-w-md rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:text-white {disabled ? 'cursor-not-allowed bg-gray-100 opacity-60 dark:bg-gray-800' : 'bg-white dark:bg-gray-800'}"
    />
{/if}
