<script>
    import { router } from '@inertiajs/svelte';
    import { route } from '../lib/route.js';
    import { clickOutside } from '../lib/clickOutside.js';

    let { title = 'Dashboard', dbStatus = 'pending', userName = '', userJobTitle = '', locale = 'vi' } = $props();

    const statusColors = { connected: '#22c55e', disconnected: '#ef4444', pending: '#facc15' };
    const statusTitles = {
        connected: 'DB Connection: Ready',
        disconnected: 'DB Connection: Error',
        pending: 'DB Connection: Pending/Checking',
    };

    let open = $state(false);
    let theme = $state(localStorage.theme || 'system');
    let logoutForm = $state(null);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function applyTheme(value) {
        theme = value;
        localStorage.theme = value;
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldBeDark = value === 'dark' || (value === 'system' && prefersDark);
        document.documentElement.classList.toggle('dark', shouldBeDark);
    }

    function initials(name) {
        return name.split(' ').map((p) => p.charAt(0)).slice(0, 2).join('');
    }

    function switchLocale(value) {
        router.post(route('account.locale'), { locale: value });
    }

    function logout() {
        // Native form submit (not router.post): the logout redirect lands on
        // Filament's login page, which is not an Inertia response — Inertia's
        // XHR-based visit can't follow that redirect into a real page load.
        logoutForm.requestSubmit();
    }
</script>

<header class="flex h-16 flex-none items-center gap-4 border-b border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-gray-900 sm:px-8">
    <h1 class="text-base font-semibold text-gray-950 dark:text-white">{title}</h1>

    <div class="ml-auto flex items-center gap-3 sm:gap-4">
        <label class="hidden items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3.5 py-2 text-gray-400 focus-within:border-accent-500 sm:flex">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 flex-none">
                <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
            </svg>
            <input type="text" placeholder="Search tenants…" class="w-48 border-none bg-transparent p-0 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0 dark:text-white" />
        </label>

        <div class="flex cursor-help items-center" title={statusTitles[dbStatus] ?? statusTitles.pending}>
            <div class="relative flex h-3 w-3">
                {#if dbStatus === 'connected'}
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75" style="background-color: {statusColors.connected};"></span>
                {/if}
                <span class="relative inline-flex h-3 w-3 rounded-full" style="background-color: {statusColors[dbStatus] ?? statusColors.pending};"></span>
            </div>
        </div>

        <div class="relative flex flex-none items-center gap-2">
            <button
                type="button"
                onclick={() => (open = !open)}
                class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white ring-gray-900/50 transition-all hover:ring-2 focus:outline-none dark:bg-gray-100 dark:text-gray-950"
            >
                {initials(userName)}
            </button>

            {#if open}
                <div
                    use:clickOutside={() => (open = false)}
                    class="absolute right-0 top-12 z-50 w-64 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white dark:bg-gray-100 dark:text-gray-950">
                            {initials(userName)}
                        </div>
                        <div class="overflow-hidden">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{userName}</p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{userJobTitle || 'System Administrator'}</p>
                        </div>
                    </div>

                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                    <div class="flex items-center justify-between gap-1 px-3 py-2">
                        <button
                            type="button"
                            onclick={() => switchLocale('vi')}
                            class="flex flex-1 justify-center rounded-md p-2 text-xs font-medium transition-colors {locale === 'vi' ? 'bg-gray-50 text-accent-600 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'}"
                            title="Tiếng Việt"
                        >
                            VI
                        </button>
                        <button
                            type="button"
                            onclick={() => switchLocale('en')}
                            class="flex flex-1 justify-center rounded-md p-2 text-xs font-medium transition-colors {locale === 'en' ? 'bg-gray-50 text-accent-600 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'}"
                            title="English"
                        >
                            EN
                        </button>
                    </div>

                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                    <div class="flex items-center justify-between gap-1 px-3 py-2">
                        <button type="button" onclick={() => applyTheme('light')} class="flex flex-1 justify-center rounded-md p-2 transition-colors {theme === 'light' ? 'bg-gray-50 text-accent-600 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'}" title="Light Mode">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>
                        <button type="button" onclick={() => applyTheme('dark')} class="flex flex-1 justify-center rounded-md p-2 transition-colors {theme === 'dark' ? 'bg-gray-50 text-accent-600 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'}" title="Dark Mode">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </button>
                        <button type="button" onclick={() => applyTheme('system')} class="flex flex-1 justify-center rounded-md p-2 transition-colors {theme === 'system' ? 'bg-gray-50 text-accent-600 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'}" title="System Theme">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>

                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                    <a href={route('account.settings')} class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700/50">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Cài đặt tài khoản
                    </a>

                    <button type="button" onclick={logout} class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700/50">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Đăng xuất
                    </button>
                </div>
            {/if}
        </div>
    </div>

    <form bind:this={logoutForm} method="POST" action={route('dashboard.logout')} class="hidden">
        <input type="hidden" name="_token" value={csrfToken} />
    </form>
</header>
