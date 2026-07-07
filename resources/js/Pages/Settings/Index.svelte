<script>
    import { router, Link } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import SettingsForm from '../../Components/Settings/SettingsForm.svelte';
    import RolesPermissionsPanel from '../../Components/Settings/RolesPermissionsPanel.svelte';
    import { route } from '../../lib/route.js';

    let { tabs, tree, activeTab, structure, section, scopeParam, scope, scopeId, scopes, values, useSystem, rolesData, categories, globalActions, hasTables } = $props();

    let showSidebar = $state(typeof window !== 'undefined' ? window.innerWidth >= 768 : true);
    let openTabs = $state(Object.fromEntries(Object.keys(tree).map((tabId) => [tabId, tabId === activeTab])));

    function changeScope(value) {
        router.get(route('settings'), { section, scope: value });
    }
</script>

<svelte:window onresize={() => (showSidebar = window.innerWidth >= 768)} />

<AppLayout title="Cấu hình hệ thống">
    <div class="mx-auto max-w-[1400px] px-2 py-6 sm:px-4 lg:px-8">
        <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="flex items-center gap-3 text-2xl font-bold text-gray-900 dark:text-white">
                    <button type="button" onclick={() => (showSidebar = !showSidebar)} class="rounded-lg p-1.5 text-gray-500 transition-colors hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700" title="Bật/Tắt Menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <svg class="hidden h-6 w-6 text-accent-500 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Cấu hình hệ thống
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 sm:ml-12">Quản lý toàn bộ cấu hình theo scope (Global / Website)</p>
            </div>

            <div class="flex w-full items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 p-1.5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-2 md:w-auto">
                <span class="ml-2 hidden text-sm font-medium text-gray-600 dark:text-gray-300 sm:inline">Store View:</span>
                <select
                    value={scopeParam}
                    onchange={(e) => changeScope(e.target.value)}
                    class="flex-1 rounded-md border-gray-300 bg-gray-50 py-1.5 pl-3 pr-8 text-sm font-medium text-gray-900 focus:border-accent-500 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white md:w-auto"
                >
                    {#each scopes as s}
                        <option value={s.value}>{s.label}</option>
                    {/each}
                </select>
            </div>
        </div>

        <div class="relative flex flex-col items-start gap-6 md:flex-row">
            {#if showSidebar}
                <div class="custom-scrollbar w-full flex-shrink-0 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 shadow-sm dark:border-gray-800 dark:bg-gray-900 md:sticky md:top-6 md:max-h-[calc(100vh-100px)] md:w-64">
                    <nav class="flex flex-col">
                        {#each Object.entries(tree) as [tabId, tab]}
                            {#if Object.keys(tab.sections).length > 0}
                                <div class="border-b border-gray-100 dark:border-gray-800">
                                    <button
                                        type="button"
                                        onclick={() => (openTabs[tabId] = !openTabs[tabId])}
                                        class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 focus:outline-none dark:bg-gray-800/50"
                                    >
                                        {tab.label}
                                        <svg class="h-4 w-4 transition-transform duration-200 {openTabs[tabId] ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    {#if openTabs[tabId]}
                                        <div class="flex flex-col border-t border-gray-100 py-1 dark:border-gray-800">
                                            {#each Object.entries(tab.sections) as [secId, sec]}
                                                <Link
                                                    href={route('settings', { section: secId, scope: scopeParam })}
                                                    class="block break-words border-l-4 px-6 py-2.5 text-sm font-medium transition-colors {section === secId ? 'border-accent-500 bg-accent-50 text-accent-600 dark:bg-accent-500/10' : 'border-transparent text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800/50'}"
                                                >
                                                    {sec.label}
                                                </Link>
                                            {/each}
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                        {/each}
                    </nav>
                </div>
            {/if}

            <div class="min-w-0 w-full flex-1">
                {#if section === 'permissions'}
                    <RolesPermissionsPanel {rolesData} {categories} {globalActions} {hasTables} />
                {:else}
                    <SettingsForm {structure} {section} {scopeParam} {scope} {values} {useSystem} />
                {/if}
            </div>
        </div>
    </div>
</AppLayout>
