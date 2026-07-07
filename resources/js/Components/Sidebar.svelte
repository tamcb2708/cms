<script>
    import { Link } from '@inertiajs/svelte';
    import { route } from '../lib/route.js';

    let { navGroups = [], userName = '' } = $props();

    const icons = {
        grid: '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        folder: '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>',
        'bar-chart': '<path d="M4 20V10"/><path d="M12 20V4"/><path d="M20 20v-6"/>',
        mail: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'credit-card': '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        users: '<circle cx="9" cy="8" r="3"/><path d="M3.5 20c0-3.6 2.5-6.5 5.5-6.5s5.5 2.9 5.5 6.5"/><circle cx="17.5" cy="9" r="2.2"/><path d="M15.8 13.8c2.4.4 4.2 2.7 4.2 5.6"/>',
        shield: '<path d="M12 3 5 6v5c0 4.5 3 7.7 7 9 4-1.3 7-4.5 7-9V6l-7-3Z"/>',
        gear: '<circle cx="12" cy="12" r="3"/><path d="M12 5V4M12 20v-1M5 12H4M20 12h-1M7.5 7.5l-.7-.7M17.2 17.2l-.7-.7M7.5 16.5l-.7.7M17.2 6.8l-.7.7"/>',
        database: '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
        software: '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="m8 10 2 2-2 2"/><path d="M12 14h4"/>',
    };

    let collapsed = $state(false);
    let openGroups = $state(
        Object.fromEntries(
            navGroups.flat().filter((item) => item.children).map((item) => [item.label, item.active]),
        ),
    );

    function navClass(active, justifyCenter = false) {
        const base = 'nav-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors';
        const justify = justifyCenter ? ' justify-center' : '';
        return active
            ? `${base}${justify} bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark`
            : `${base}${justify} text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5`;
    }

    function childClass(active) {
        const base = 'block rounded-md px-3 py-2 text-sm font-medium transition-colors';
        return active
            ? `${base} bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark`
            : `${base} text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5`;
    }
</script>

<aside
    class="flex flex-none flex-col gap-6 overflow-y-auto border-r border-gray-200 bg-white py-6 transition-[width] duration-300 dark:border-white/10 dark:bg-gray-900 {collapsed ? 'w-[4.5rem] px-3' : 'w-64 px-4'}"
>
    <div class="flex items-center justify-between px-2">
        {#if !collapsed}
            <Link href={route('dashboard')} class="flex items-baseline gap-2">
                <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">Eios Cms</span>
            </Link>
        {/if}
        <button
            onclick={() => (collapsed = !collapsed)}
            class="rounded p-1 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
            title="Thu gọn/Mở rộng Sidebar"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <nav class="flex flex-col gap-1">
        {#each navGroups as items, groupIndex}
            {#if groupIndex > 0}
                <hr class="my-2 border-gray-100 dark:border-white/10" />
            {/if}

            {#each items as item}
                {#if item.children}
                    <div class="flex flex-col">
                        <button
                            onclick={() => (openGroups[item.label] = !openGroups[item.label])}
                            class="{navClass(item.active)} w-full {collapsed ? 'justify-center' : 'justify-between'}"
                            title={item.label}
                        >
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-[18px] w-[18px] flex-none">
                                    {@html icons[item.icon]}
                                </svg>
                                {#if !collapsed}
                                    <span class="truncate">{item.label}</span>
                                {/if}
                            </div>
                            {#if !collapsed}
                                <svg
                                    class="h-4 w-4 shrink-0 transition-transform duration-200"
                                    class:rotate-180={openGroups[item.label]}
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            {/if}
                        </button>
                        {#if openGroups[item.label] && !collapsed}
                            <div class="space-y-1 py-1 pl-9 pr-2">
                                {#each item.children as child}
                                    <Link href={child.href} class={childClass(child.active)}>
                                        {child.label}
                                    </Link>
                                {/each}
                            </div>
                        {/if}
                    </div>
                {:else}
                    <Link href={item.href} class={navClass(item.active, collapsed)} title={item.label}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-[18px] w-[18px] flex-none">
                            {@html icons[item.icon]}
                        </svg>
                        {#if !collapsed}
                            <span class="truncate">{item.label}</span>
                        {/if}
                    </Link>
                {/if}
            {/each}
        {/each}
    </nav>

    {#if !collapsed}
        <div class="mt-auto rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 text-xs text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
            Signed in as
            <span class="block truncate font-medium text-gray-900 dark:text-white">{userName}</span>
        </div>
    {/if}
</aside>
