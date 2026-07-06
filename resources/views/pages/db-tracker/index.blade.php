@extends('layouts.dashboard')

@section('title', 'DB Tracker')

@section('content')
<div class="flex flex-col h-full gap-4">
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-2">
        @for($i = 1; $i <= 3; $i++)
            <button 
                onclick="switchTab({{ $i }})"
                id="tab-btn-{{ $i }}"
                class="px-4 py-2 text-sm font-medium rounded-t-lg transition-colors {{ $i === 1 ? 'bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark border-b-2 border-accent-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-b-2 border-transparent' }}"
            >
                {{ __('messages.instance') }} {{ $i }}
            </button>
        @endfor
    </div>

    <div class="flex-1 relative bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden">
        @for($i = 1; $i <= 3; $i++)
            <div id="tab-content-{{ $i }}" class="absolute inset-0 w-full h-full overflow-y-auto {{ $i === 1 ? 'block' : 'hidden' }}">
                <div class="flex items-center justify-center h-full text-gray-500">
                    <svg class="animate-spin h-6 w-6 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    {{ __('messages.loading') }}
                </div>
            </div>
        @endfor
    </div>
</div>
@endsection

@push('scripts')
<script>
    let loadedTabs = {};
    
    function loadTab(tabId) {
        if (loadedTabs[tabId]) return; // Already loaded or loading
        loadedTabs[tabId] = true;
        
        const container = document.getElementById('tab-content-' + tabId);
        container.innerHTML = `
            <div class="flex items-center justify-center h-full text-gray-500">
                <svg class="animate-spin h-6 w-6 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Đang tải...
            </div>`;
        
        fetch(`{{ route('db-tracker.tab') }}?tab=${tabId}&_t=${new Date().getTime()}`, { credentials: 'same-origin' })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                
                const scripts = container.getElementsByTagName('script');
                const scriptsToRun = [];
                for (let i = 0; i < scripts.length; i++) {
                    scriptsToRun.push(scripts[i].innerText);
                }
                for (let i = 0; i < scriptsToRun.length; i++) {
                    const newScript = document.createElement('script');
                    newScript.text = scriptsToRun[i];
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                }

                if (typeof window['initDbTrackerTab' + tabId] === 'function') {
                    window['initDbTrackerTab' + tabId]();
                }
            });
    }

    function switchTab(tabId) {
        for(let i = 1; i <= 3; i++) {
            const btn = document.getElementById('tab-btn-' + i);
            const content = document.getElementById('tab-content-' + i);
            
            if (i === tabId) {
                btn.className = 'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors bg-accent-50 text-accent-600 dark:bg-accent-500/10 dark:text-accent-dark border-b-2 border-accent-500';
                content.className = 'absolute inset-0 w-full h-full overflow-y-auto block';
                loadTab(tabId);
                
                // Restart polling if returning to an already loaded tab
                if (loadedTabs[tabId] && typeof window['fetchLogs' + tabId] === 'function') {
                    if (!window['dbTrackerInterval' + tabId]) {
                        window['dbTrackerInterval' + tabId] = setInterval(window['fetchLogs' + tabId], 3000);
                        window['fetchLogs' + tabId](); // Instant fetch on switch
                    }
                }
            } else {
                btn.className = 'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-b-2 border-transparent';
                content.className = 'absolute inset-0 w-full h-full overflow-y-auto hidden';
                
                // Stop polling for hidden tabs
                if (window['dbTrackerInterval' + i]) {
                    clearInterval(window['dbTrackerInterval' + i]);
                    window['dbTrackerInterval' + i] = null;
                }
            }
        }
    }

    // Init first tab
    switchTab(1);
</script>
@endpush
