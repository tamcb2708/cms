@extends('layouts.dashboard')

@section('title', __('messages.data_activity'))

@section('content')
<div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent-500 to-purple-500 bg-clip-text text-transparent">⚡ {{ __('messages.db_tracker') }} - {{ __('messages.data_activity') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.connected_to') }} <strong>{{ $creds['dbname'] }} @ {{ $creds['host'] }}</strong></p>
        </div>
    </div>

    @if(!$isInitialized)
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center p-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-white/5 max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('messages.system_not_initialized') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('messages.system_not_initialized_desc') }}</p>
                <button onclick="actionDbTracker({{ $tab }}, 'init')" class="px-4 py-2 bg-accent-600 hover:bg-accent-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    {{ __('messages.initialize_system') }}
                </button>
            </div>
        </div>
    @else
        @include('pages.db-tracker.css')
        <div class="dbt-grid">
            <!-- Left: Tables -->
            <div class="dbt-card dbt-col-1" id="sidebar-{{ $tab }}">
                <div class="dbt-header flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <h3 class="font-medium flex items-center gap-2">{{ __('messages.table_list') }}</h3>
                        <button onclick="loadedTabs[{{ $tab }}] = false; loadTab({{ $tab }});" class="text-xs font-medium dbt-text-accent flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            {{ __('messages.reload') }}
                        </button>
                    </div>
                    <div class="relative">
                        <input type="text" id="table-search-{{ $tab }}" placeholder="{{ __('messages.search_tables') }}" class="w-full text-sm px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500" onkeyup="filterTables{{ $tab }}()">
                    </div>
                </div>
                <div id="table-list-{{ $tab }}" class="dbt-scroll-y p-4 flex flex-col gap-2" style="flex: 1;">
                    @foreach($tables as $t)
                        <div class="table-item dbt-item flex items-center justify-between p-3 rounded-lg" data-name="{{ $t['name'] }}">
                            <div>
                                <div class="font-medium text-sm">{{ $t['name'] }}</div>
                                @if($t['tracked'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400 mt-1">{{ __('messages.tracking') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800 dark:bg-gray-500/10 dark:text-gray-400 mt-1">{{ __('messages.not_tracking') }}</span>
                                @endif
                            </div>
                            <div>
                                @if($t['tracked'])
                                    <button onclick="actionDbTracker({{ $tab }}, 'untrack', '{{ $t['name'] }}')" class="text-xs font-medium dbt-text-red">{{ __('messages.turn_off') }}</button>
                                @else
                                    <button onclick="actionDbTracker({{ $tab }}, 'track', '{{ $t['name'] }}')" class="text-xs font-medium dbt-text-green">{{ __('messages.turn_on') }}</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Logs -->
            <div class="dbt-card dbt-col-2">
                <div class="dbt-header flex justify-between items-center">
                    <h3 class="font-medium flex items-center gap-2">
                        <button onclick="document.getElementById('sidebar-{{ $tab }}').classList.toggle('dbt-collapsed')" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" title="Bật/Tắt danh sách bảng">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        {{ __('messages.recent_activity') }}
                        <span id="sync-status-{{ $tab }}" class="flex items-center gap-1.5 text-[10px] font-medium dbt-badge-green px-2 py-0.5 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> {{ __('messages.sync') }}</span>
                    </h3>
                    <div class="flex items-center gap-4">
                        <button onclick="if(typeof window['fetchLogs{{ $tab }}'] === 'function') window['fetchLogs{{ $tab }}']();" class="text-xs font-medium dbt-text-accent flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            {{ __('messages.reload') }}
                        </button>
                        <button onclick="if(confirm('{{ __('messages.confirm_clear_logs') }}')) actionDbTracker({{ $tab }}, 'clear')" class="text-xs font-medium dbt-text-red">{{ __('messages.clear_logs') }}</button>
                    </div>
                </div>
                <div class="dbt-table-wrapper">
                    <table class="dbt-table">
                        <thead class="sticky top-0 z-10">
                            <tr>
                                <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider">{{ __('messages.time') }}</th>
                                <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider">{{ __('messages.table') }}</th>
                                <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider">{{ __('messages.action') }}</th>
                                <th class="dbt-table-th p-3 text-xs font-medium uppercase tracking-wider w-full">{{ __('messages.data') }}</th>
                            </tr>
                        </thead>
                        <tbody id="logs-tbody-{{ $tab }}">
                            <!-- Logs will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function actionDbTracker(tabId, action, table = null) {
        let url = `{{ route('db-tracker.action') }}`;
        let formData = new FormData();
        formData.append('tab', tabId);
        formData.append('action', action);
        if (table) formData.append('table', table);

        fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                if(window.showToast) {
                    window.showToast("Lỗi", data.message || 'Lỗi thực thi', 'error');
                } else {
                    alert(data.message || 'Lỗi thực thi');
                }
            }
        })
        .catch(err => {
            if(window.showToast) {
                window.showToast("Lỗi", 'Có lỗi xảy ra kết nối Server: ' + err.message, 'error');
            } else {
                alert('Có lỗi xảy ra kết nối Server: ' + err.message);
            }
        });
    }

    window['initDbTrackerTab{{ $tab }}'] = function() {
        @if($isInitialized)
            window['fetchLogs{{ $tab }}'] = function() {
                fetch(`{{ route('db-tracker.logs') }}?tab={{ $tab }}&_t=${new Date().getTime()}`, { credentials: 'same-origin' })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.text();
                    })
                    .then(html => {
                        const tbody = document.getElementById('logs-tbody-{{ $tab }}');
                        if (tbody) {
                            tbody.innerHTML = html;
                        }
                        const syncStatus = document.getElementById('sync-status-{{ $tab }}');
                        if(syncStatus) {
                            syncStatus.className = "flex items-center gap-1.5 text-[10px] font-medium dbt-badge-green px-2 py-0.5 rounded-full";
                            syncStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Đồng bộ';
                        }
                    })
                    .catch(error => {
                        const syncStatus = document.getElementById('sync-status-{{ $tab }}');
                        if(syncStatus) {
                            syncStatus.className = "flex items-center gap-1.5 text-[10px] font-medium dbt-badge-red px-2 py-0.5 rounded-full";
                            syncStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Mất kết nối';
                        }
                    });
            };
            
            window['fetchLogs{{ $tab }}']();
            if(window['dbTrackerInterval{{ $tab }}']) {
                clearInterval(window['dbTrackerInterval{{ $tab }}']);
            }
            window['dbTrackerInterval{{ $tab }}'] = setInterval(window['fetchLogs{{ $tab }}'], 3000);
        @endif
    };

    function filterTables{{ $tab }}() {
        const input = document.getElementById('table-search-{{ $tab }}');
        const filter = input.value.toLowerCase();
        const container = document.getElementById('table-list-{{ $tab }}');
        const items = container.getElementsByClassName('table-item');
        
        for (let i = 0; i < items.length; i++) {
            const name = items[i].getAttribute('data-name');
            if (name.toLowerCase().indexOf(filter) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window['initDbTrackerTab{{ $tab }}'] === 'function') {
            window['initDbTrackerTab{{ $tab }}']();
        }
    });
</script>
@endpush
