@extends('layouts.dashboard')

@section('title', __('messages.performance'))

@section('content')
<div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent-500 to-purple-500 bg-clip-text text-transparent">⚡ {{ __('messages.db_tracker') }} - {{ __('messages.performance') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.connected_to') }} <strong>{{ $creds['dbname'] }} @ {{ $creds['host'] }}</strong></p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-full overflow-hidden shadow-sm px-1 py-1">
                <label for="refresh-interval" class="px-2.5 text-[11px] font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.perf_refresh') }}</label>
                <select id="refresh-interval" class="text-xs font-bold bg-gray-50 dark:bg-gray-900 border-none rounded-full py-1 pl-3 pr-8 text-accent-600 dark:text-accent-400 focus:ring-2 focus:ring-accent-500 cursor-pointer appearance-none outline-none shadow-inner" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23888%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right .7rem top 50%; background-size: .65rem auto;" onchange="changeInterval()">
                    <option value="5000">{{ __('messages.perf_5s') }}</option>
                    <option value="30000">{{ __('messages.perf_30s') }}</option>
                    <option value="60000">{{ __('messages.perf_1m') }}</option>
                    <option value="3600000">{{ __('messages.perf_1h') }}</option>
                    <option value="21600000">{{ __('messages.perf_6h') }}</option>
                    <option value="86400000">{{ __('messages.perf_1d') }}</option>
                    <option value="604800000">{{ __('messages.perf_7d') }}</option>
                    <option value="259200000">{{ __('messages.perf_30d') }}</option>
                    <option value="7776000000">{{ __('messages.perf_3mo') }}</option>
                    <option value="15552000000">{{ __('messages.perf_6mo') }}</option>
                    <option value="0">{{ __('messages.perf_pause') }}</option>
                </select>
            </div>
            <span id="sync-status" class="flex items-center gap-1.5 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400 px-3 py-1.5 rounded-full border border-green-200 dark:border-green-500/20 shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> {{ __('messages.sync') }}
            </span>
        </div>
    </div>

    <!-- Metrics Cards Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5 flex flex-col items-center justify-center">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.perf_cache_hit') }}</h4>
            <div id="cache-hit-val" class="text-3xl font-bold text-gray-900 dark:text-white">--%</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5 flex flex-col items-center justify-center">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.perf_total_conn') }}</h4>
            <div id="total-conn-val" class="text-3xl font-bold text-accent-600">--</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5 flex flex-col items-center justify-center">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.perf_active_queries') }}</h4>
            <div id="active-queries-val" class="text-3xl font-bold text-green-500">--</div>
        </div>
    </div>

    <!-- Metrics Cards Row 2 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5 flex flex-col items-center justify-center relative group">
            <button class="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-accent-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors opacity-0 group-hover:opacity-100" onclick="setSoftLimit()" title="Cài đặt định mức (Soft Limit)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </button>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.perf_db_size') }}</h4>
            <div id="db-size-val" class="text-2xl font-bold text-gray-900 dark:text-white">-- <span class="text-sm font-normal text-gray-400">/ --</span></div>
            
            <div class="w-full mt-3 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 relative overflow-hidden">
                <div id="db-size-progress" class="bg-purple-500 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
            <div class="w-full mt-1.5 flex justify-between text-[10px] text-gray-400">
                <span id="db-size-percent">0%</span>
                <span>Soft Limit</span>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5 flex flex-col items-center justify-center">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.perf_waiting_locks') }}</h4>
            <div id="waiting-locks-val" class="text-3xl font-bold text-yellow-500">--</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5 flex flex-col items-center justify-center">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1" title="Tổng số giao dịch: Commits (xanh) / Rollbacks (đỏ)">{{ __('messages.perf_transactions') }} <span class="text-[10px] font-normal opacity-70">(C/R)</span></h4>
            <div class="flex items-center gap-3">
                <div class="text-2xl font-bold text-green-500" id="xact-commit-val" title="{{ __('messages.perf_commits') }}">--</div>
                <div class="text-gray-300 dark:text-gray-600">/</div>
                <div class="text-2xl font-bold text-red-500" id="xact-rollback-val" title="{{ __('messages.perf_rollbacks') }}">--</div>
            </div>
            <div class="mt-2 text-[10px] text-gray-400 text-center leading-tight">
                <span class="text-green-500 font-medium">Commits</span> (Thành công) / <span class="text-red-500 font-medium">Rollbacks</span> (Bị hủy)
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 mb-6">
        <!-- Chart Container -->
        <div class="w-full lg:w-1/3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-4">
            <h3 class="font-medium mb-4 text-center">{{ __('messages.perf_conn_states') }}</h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="statesChart"></canvas>
            </div>
        </div>

        <!-- Live Connections Table -->
        <div class="w-full lg:w-2/3 flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden flex flex-col h-80">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                    <h3 class="font-medium text-sm">{{ __('messages.perf_live_connections') }}</h3>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500" id="live-conn-refresh-label"></span>
            </div>
            <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PID</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_user') }}</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_client_ip') }}</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_application') }}</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_duration') }}</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_state') }}</th>
                        </tr>
                    </thead>
                    <tbody id="activities-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400">{{ __('messages.loading') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Slow Queries Table -->
    <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden flex flex-col mb-6">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="font-medium text-sm">{{ __('messages.perf_slow_queries') }}</h3>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500">Top 5</span>
        </div>
        <div class="overflow-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PID</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_user') }}</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.perf_duration') }}</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Query</th>
                    </tr>
                </thead>
                <tbody id="slow-queries-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-400">{{ __('messages.loading') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let statesChart = null;

    function formatDuration(sec) {
        if (sec === null || sec === undefined) return '—';
        sec = parseFloat(sec);
        if (sec < 1) return `${Math.round(sec * 1000)}ms`;
        if (sec < 60) return `${sec.toFixed(1)}s`;
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return `${m}m ${s}s`;
    }

    function initChart() {
        const ctx = document.getElementById('statesChart').getContext('2d');
        statesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['{{ __('messages.perf_state_active') }}', '{{ __('messages.perf_state_idle') }}', '{{ __('messages.perf_state_other') }}'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: [
                        '#10B981', // green
                        '#6B7280', // gray
                        '#F59E0B'  // yellow
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151'
                        }
                    }
                }
            }
        });
    }

    function fetchStats() {
        fetch(`{{ route('db-tracker.performance.stats') }}?_t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    setSyncStatus(false);
                    if (refreshTimer) clearInterval(refreshTimer);
                    return;
                }
                setSyncStatus(true);

                // Row 1 Metrics
                document.getElementById('cache-hit-val').textContent = data.cacheHit + '%';
                
                let activeCount = 0;
                let idleCount = 0;
                let otherCount = 0;
                let totalCount = 0;

                data.states.forEach(s => {
                    totalCount += s.count;
                    if(s.state === 'active') activeCount += s.count;
                    else if(s.state === 'idle') idleCount += s.count;
                    else otherCount += s.count;
                });

                document.getElementById('total-conn-val').textContent = totalCount;
                document.getElementById('active-queries-val').textContent = activeCount;

                // DB Size & Progress
                const dbSizeBytes = data.dbStats.db_size_bytes || 0;
                const dbSizePretty = data.dbStats.db_size || '--';
                const limitBytes = dbSoftLimitGB * 1024 * 1024 * 1024;
                let pct = limitBytes > 0 ? (dbSizeBytes / limitBytes) * 100 : 0;
                let displayPct = pct;
                if(pct > 100) pct = 100;
                
                document.getElementById('db-size-val').innerHTML = `${dbSizePretty} <span class="text-sm font-normal text-gray-400">/ ${dbSoftLimitGB} GB</span>`;
                
                const progBar = document.getElementById('db-size-progress');
                progBar.style.width = pct + '%';
                
                if(pct > 90) progBar.className = "bg-red-500 h-1.5 rounded-full transition-all duration-500";
                else if(pct > 75) progBar.className = "bg-yellow-500 h-1.5 rounded-full transition-all duration-500";
                else progBar.className = "bg-purple-500 h-1.5 rounded-full transition-all duration-500";
                
                document.getElementById('db-size-percent').textContent = displayPct.toFixed(1) + '%';

                document.getElementById('waiting-locks-val').textContent = data.waitingLocks || 0;
                
                // Format large numbers
                const fmt = new Intl.NumberFormat();
                document.getElementById('xact-commit-val').textContent = data.dbStats.xact_commit ? fmt.format(data.dbStats.xact_commit) : '--';
                document.getElementById('xact-rollback-val').textContent = data.dbStats.xact_rollback ? fmt.format(data.dbStats.xact_rollback) : '--';

                // Chart
                if(statesChart) {
                    statesChart.data.datasets[0].data = [activeCount, idleCount, otherCount];
                    statesChart.update();
                }

                // Table — Live Connections
                const tbody = document.getElementById('activities-body');
                let html = '';
                if(data.activities.length === 0) {
                    html = `<tr><td colspan="6" class="text-center py-8 text-gray-400 dark:text-gray-500"><div class="flex flex-col items-center gap-2"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg><span>${i18n.noConnections}</span></div></td></tr>`;
                } else {
                    data.activities.forEach(act => {
                        // State badge
                        let stateBadge = '';
                        if(act.state === 'active')
                            stateBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Active</span>';
                        else if(act.state === 'idle')
                            stateBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-600/30 dark:text-gray-400"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Idle</span>';
                        else
                            stateBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>${act.state || 'unknown'}</span>`;

                        // IP display
                        const ip = act.client_addr
                            ? `<span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-700 dark:text-gray-300">${act.client_addr}:${act.client_port ?? ''}</span>`
                            : `<span class="text-xs text-gray-400 italic">${i18n.localSocket}</span>`;

                        // Duration
                        const dur = act.duration_sec != null
                            ? formatDuration(act.duration_sec)
                            : '<span class="text-gray-400">—</span>';

                        // App name
                        const app = act.application_name
                            ? `<span class="text-xs text-gray-500 dark:text-gray-400">${act.application_name}</span>`
                            : '<span class="text-gray-400">—</span>';

                        html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-3 py-2.5 text-xs font-mono text-gray-500 dark:text-gray-400">${act.pid}</td>
                            <td class="px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-white">${act.usename}</td>
                            <td class="px-3 py-2.5">${ip}</td>
                            <td class="px-3 py-2.5">${app}</td>
                            <td class="px-3 py-2.5 text-xs text-gray-500 dark:text-gray-400 font-mono">${dur}</td>
                            <td class="px-3 py-2.5">${stateBadge}</td>
                        </tr>`;
                    });
                }
                tbody.innerHTML = html;

                // Table — Slow Queries
                const slowBody = document.getElementById('slow-queries-body');
                let slowHtml = '';
                if(data.slowQueries.length === 0) {
                    slowHtml = `<tr><td colspan="4" class="text-center py-6 text-gray-400 dark:text-gray-500">No active slow queries.</td></tr>`;
                } else {
                    data.slowQueries.forEach(sq => {
                        const dur = formatDuration(sq.running_time_sec);
                        slowHtml += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-3 py-2.5 text-xs font-mono text-gray-500 dark:text-gray-400">${sq.pid}</td>
                            <td class="px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-white">${sq.usename}</td>
                            <td class="px-3 py-2.5 text-xs text-orange-600 dark:text-orange-400 font-mono font-medium">${dur}</td>
                            <td class="px-3 py-2.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="max-w-2xl truncate" title="${sq.query}">${sq.query}</div>
                            </td>
                        </tr>`;
                    });
                }
                slowBody.innerHTML = slowHtml;
            })
            .catch(err => {
                setSyncStatus(false);
            });
    }

    const i18n = {
        syncing:        '{{ __('messages.sync') }}',
        disconnected:   '{{ __('messages.perf_disconnected') }}',
        noConnections:  '{{ __('messages.perf_no_connections') }}',
        localSocket:    '{{ __('messages.perf_local_socket') }}',
    };

    function setSyncStatus(isOk) {
        const span = document.getElementById('sync-status');
        if(isOk) {
            span.className = "flex items-center gap-1.5 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400 px-3 py-1 rounded-full border border-green-200 dark:border-green-500/20";
            span.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> ${i18n.syncing}`;
        } else {
            span.className = "flex items-center gap-1.5 text-xs font-medium text-red-600 bg-red-50 dark:bg-red-500/10 dark:text-red-400 px-3 py-1 rounded-full border border-red-200 dark:border-red-500/20";
            span.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> ${i18n.disconnected}`;
        }
    }

    let dbSoftLimitGB = localStorage.getItem('db_tracker_soft_limit') || 10;
    let refreshTimer = null;

    function setSoftLimit() {
        let limit = prompt("{{ __('messages.perf_soft_limit_prompt') }}", dbSoftLimitGB);
        if(limit !== null && !isNaN(limit) && limit > 0) {
            dbSoftLimitGB = limit;
            localStorage.setItem('db_tracker_soft_limit', limit);
            fetchStats(); // Cập nhật ngay
        }
    }

    function changeInterval() {
        const select = document.getElementById('refresh-interval');
        const val = parseInt(select.value);
        const text = select.options[select.selectedIndex].text;
        localStorage.setItem('db_tracker_refresh_interval', val);
        
        if (refreshTimer) clearInterval(refreshTimer);
        
        const liveConnLabel = document.getElementById('live-conn-refresh-label');
        if (liveConnLabel) {
            liveConnLabel.textContent = val > 0 ? (document.documentElement.lang === 'vi' ? 'Làm mới: ' : 'Refresh: ') + text : (document.documentElement.lang === 'vi' ? 'Tạm dừng' : 'Paused');
        }

        // JS setInterval max limit is 2147483647 ms (~24.8 days). 
        // If larger, we don't set a timer because it will overflow to 1ms.
        if (val > 0 && val <= 2147483647) {
            refreshTimer = setInterval(fetchStats, val);
            setSyncStatus(true);
        } else {
            // Stopped or Exceeds Limit
            const span = document.getElementById('sync-status');
            span.className = "flex items-center gap-1.5 text-xs font-medium text-gray-600 bg-gray-50 dark:bg-gray-500/10 dark:text-gray-400 px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-500/20 shadow-sm";
            const textLabel = val > 2147483647 ? "{{ __('messages.perf_long_cycle') }}" : "{{ __('messages.perf_stopped') }}";
            span.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> ${textLabel}`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initChart();
        fetchStats();
        
        // Restore previous interval setting
        const savedInterval = localStorage.getItem('db_tracker_refresh_interval');
        if (savedInterval !== null) {
            document.getElementById('refresh-interval').value = savedInterval;
        }
        
        changeInterval(); // Start the timer
    });
</script>
@endpush
