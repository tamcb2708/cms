<header class="flex h-16 flex-none items-center gap-4 border-b border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-gray-900 sm:px-8">
    <h1 class="text-base font-semibold text-gray-950 dark:text-white">@yield('title', 'Dashboard')</h1>

    <div class="ml-auto flex items-center gap-3 sm:gap-4">
        <!-- Search -->
        <label class="hidden items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3.5 py-2 text-gray-400 focus-within:border-accent-500 sm:flex">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 flex-none">
                <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" placeholder="Search tenants…" class="w-48 border-none bg-transparent p-0 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0 dark:text-white">
        </label>

        <!-- DB Status Indicator -->
        @php
            $dbStatus = \App\Models\CoreConfig::where('path', 'database/tracker/status')->value('value') ?? 'pending';
            $statusColors = [
                'connected' => '#22c55e', // green-500
                'disconnected' => '#ef4444', // red-500
                'pending' => '#facc15', // yellow-400
            ];
            $statusTitles = [
                'connected' => 'DB Connection: Ready',
                'disconnected' => 'DB Connection: Error',
                'pending' => 'DB Connection: Pending/Checking',
            ];
            $bgColor = $statusColors[$dbStatus] ?? $statusColors['pending'];
            $title = $statusTitles[$dbStatus] ?? $statusTitles['pending'];
        @endphp
        <div class="flex items-center cursor-help" title="{{ $title }}">
            <div class="relative flex h-3 w-3">
                @if($dbStatus === 'connected')
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: {{ $bgColor }};"></span>
                @endif
                <span class="relative inline-flex rounded-full h-3 w-3" style="background-color: {{ $bgColor }};"></span>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="flex flex-none items-center gap-2 relative" x-data="{ open: false, theme: localStorage.theme || 'system' }" @click.away="open = false" x-init="$watch('theme', val => { localStorage.theme = val; if(val === 'dark') document.documentElement.classList.add('dark'); else if (val === 'light') document.documentElement.classList.remove('dark'); else { if (window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); } })">
            <button type="button" @click="open = !open" class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white dark:bg-gray-100 dark:text-gray-950 focus:outline-none hover:ring-2 ring-gray-900/50 transition-all">
                {{ collect(explode(' ', auth()->user()?->name ?? ''))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
            </button>
        
        <!-- Dropdown Menu -->
        <div x-show="open" x-transition class="absolute right-0 top-12 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 z-50" style="display: none;">
            <div class="px-4 py-3 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white dark:bg-gray-100 dark:text-gray-950">
                    {{ collect(explode(' ', auth()->user()?->name ?? ''))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()?->job_title ?? 'System Administrator' }}</p>
                </div>
            </div>
            
            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
            
            <div class="px-3 py-2 flex items-center justify-between gap-1">
                <form action="{{ route('account.locale') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="locale" value="vi">
                    <button type="submit" class="w-full p-2 rounded-md transition-colors flex justify-center text-xs font-medium {{ app()->getLocale() === 'vi' ? 'text-accent-600 bg-gray-50 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}" title="Tiếng Việt">
                        VI
                    </button>
                </form>
                <form action="{{ route('account.locale') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" class="w-full p-2 rounded-md transition-colors flex justify-center text-xs font-medium {{ app()->getLocale() === 'en' ? 'text-accent-600 bg-gray-50 dark:bg-gray-700/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}" title="English">
                        EN
                    </button>
                </form>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
            
            <div class="px-3 py-2 flex items-center justify-between gap-1">
                <button type="button" @click="theme = 'light'" :class="{'text-accent-600 bg-gray-50 dark:bg-gray-700/50': theme === 'light', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': theme !== 'light'}" class="p-2 rounded-md transition-colors flex-1 flex justify-center" title="Light Mode">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
                <button type="button" @click="theme = 'dark'" :class="{'text-accent-600 bg-gray-50 dark:bg-gray-700/50': theme === 'dark', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': theme !== 'dark'}" class="p-2 rounded-md transition-colors flex-1 flex justify-center" title="Dark Mode">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                <button type="button" @click="theme = 'system'" :class="{'text-accent-600 bg-gray-50 dark:bg-gray-700/50': theme === 'system', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': theme !== 'system'}" class="p-2 rounded-md transition-colors flex-1 flex justify-center" title="System Theme">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </button>
            </div>
            
            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
            
            <a href="{{ route('account.settings') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                {{ __('messages.account_settings') }}
            </a>

            <form method="POST" action="{{ route('dashboard.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    {{ __('messages.sign_out') }}
                </button>
            </form>
        </div>
    </div>
</div>
</header>
