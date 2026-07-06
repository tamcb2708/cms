@extends('layouts.dashboard')

@section('title', __('messages.db_tracker_backups'))

@section('content')
<div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent-500 to-purple-500 bg-clip-text text-transparent">⚡ {{ __('messages.db_tracker') }} - {{ __('messages.db_tracker_backups') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.connected_to') }} <strong>{{ $creds['dbname'] }} @ {{ $creds['host'] }}</strong></p>
        </div>
        <button onclick="alert('{{ __('messages.db_backups_alert_create') }}')" class="flex items-center gap-2 bg-accent-600 hover:bg-accent-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            {{ __('messages.db_backups_create_btn') }}
        </button>
    </div>

    <!-- AWS RDS Snapshots Table -->
    <div class="flex-1 min-h-0 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden flex flex-col mb-6">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
            <h3 class="font-medium flex items-center gap-2 text-gray-900 dark:text-white">
                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.96 2.06c-.63 0-1.25.16-1.8.46L4.72 5.67A3.67 3.67 0 002.85 8.9v6.2c0 1.34.73 2.57 1.87 3.23l5.44 3.15c1.1.64 2.45.64 3.55 0l5.44-3.15c1.14-.66 1.87-1.89 1.87-3.23V8.9c0-1.34-.73-2.57-1.87-3.23l-5.44-3.15a3.67 3.67 0 00-1.75-.46zm0 1.48c.36 0 .72.1.1.02.26l5.45 3.15c.6.35.98 1.02.98 1.71v6.2c0 .69-.38 1.36-.98 1.71l-5.45 3.15c-.6.35-1.37.35-1.97 0L4.54 16.3a1.96 1.96 0 01-.98-1.71V8.4c0-.69.38-1.36.98-1.71l5.45-3.15c.3-.18.64-.26.97-.26zM12 7.02c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5zm0 1.5c1.94 0 3.5 1.56 3.5 3.5S13.94 15.5 12 15.5 8.5 13.94 8.5 12 10.06 8.52 12 8.52z"></path></svg>
                {{ __('messages.db_backups_rds_title') }}
            </h3>
            <span class="text-xs text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">{{ __('messages.db_backups_region') }}</span>
        </div>
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/80 sticky top-0 backdrop-blur-sm z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.db_backups_snap_id') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.db_backups_type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.db_backups_created_at') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.db_backups_size') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.db_backups_status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.db_backups_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($snapshots as $snap)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-accent-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            {{ $snap['id'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($snap['type'] == 'Automated')
                                <span class="bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 px-2 py-0.5 rounded text-[11px] uppercase tracking-wide font-bold">{{ __('messages.db_backups_type_auto') }}</span>
                            @else
                                <span class="bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 px-2 py-0.5 rounded text-[11px] uppercase tracking-wide font-bold">{{ __('messages.db_backups_type_manual') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $snap['created_at'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $snap['size'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1.5 font-medium"><span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></span> {{ __('messages.db_backups_status_avail') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="alert('{{ __('messages.db_backups_alert_restore') }}')" class="flex items-center justify-end w-full gap-1.5 text-accent-600 dark:text-accent-400 hover:text-accent-800 dark:hover:text-accent-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                {{ __('messages.db_backups_btn_restore') }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 shrink-0">
        <div class="bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-900/20 dark:to-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-500/10 p-5 group hover:shadow-md transition-shadow">
            <h3 class="font-bold text-lg mb-2 flex items-center gap-2 text-indigo-800 dark:text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ __('messages.db_backups_pitr_title') }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 leading-relaxed">
                {{ __('messages.db_backups_pitr_desc') }}
            </p>
        </div>
        
        <div class="bg-gradient-to-br from-amber-50 to-white dark:from-amber-900/20 dark:to-gray-800 rounded-xl shadow-sm border border-amber-100 dark:border-amber-500/10 p-5 group hover:shadow-md transition-shadow">
            <h3 class="font-bold text-lg mb-2 flex items-center gap-2 text-amber-800 dark:text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                {{ __('messages.db_backups_s3_title') }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 leading-relaxed">
                {{ __('messages.db_backups_s3_desc') }}
            </p>
        </div>
    </div>
</div>
@endsection
