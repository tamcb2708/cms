@extends('layouts.dashboard')

@section('title', __('messages.security_users'))

@section('content')
<div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent-500 to-purple-500 bg-clip-text text-transparent">⚡ {{ __('messages.db_tracker') }} - {{ __('messages.security_users') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.connected_to') }} <strong>{{ $creds['dbname'] }} @ {{ $creds['host'] }}</strong></p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 flex-1 min-h-0">
        <!-- Roles Table Column -->
        <div class="xl:col-span-2 flex flex-col bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="font-medium">Database Roles & Users</h3>
            </div>
            <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.sec_role_name') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.sec_superuser') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.sec_create_role') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.sec_create_db') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.sec_can_login') }}</th>
                        </tr>
                    </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $role['role_name'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($role['rolsuper']) 
                                <span class="inline-flex items-center justify-center bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 rounded-full p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            @else 
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($role['rolcreaterole']) 
                                <span class="inline-flex items-center justify-center bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 rounded-full p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            @else 
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($role['rolcreatedb']) 
                                <span class="inline-flex items-center justify-center bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 rounded-full p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            @else 
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($role['rolcanlogin']) 
                                <span class="inline-flex items-center justify-center bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 rounded-full p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            @else 
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>

        <!-- Security Advisory Column -->
        <div class="flex flex-col gap-6 overflow-y-auto">
            <!-- Advisory Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-500/20 p-5 relative">
                <div class="flex items-center gap-3 mb-4 text-red-600 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="font-bold text-lg">{{ __('messages.sec_advisory_title') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-5 leading-relaxed">
                    {{ __('messages.sec_advisory_desc') }}
                </p>
                
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <strong class="block text-sm text-gray-900 dark:text-white">{{ __('messages.sec_rule_1_title') }}</strong>
                            <span class="text-xs text-gray-500 leading-relaxed">{{ __('messages.sec_rule_1_desc') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <strong class="block text-sm text-gray-900 dark:text-white">{{ __('messages.sec_rule_2_title') }}</strong>
                            <span class="text-xs text-gray-500 leading-relaxed">{{ __('messages.sec_rule_2_desc') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div>
                            <strong class="block text-sm text-gray-900 dark:text-white">{{ __('messages.sec_rule_3_title') }}</strong>
                            <span class="text-xs text-gray-500 leading-relaxed">{{ __('messages.sec_rule_3_desc') }}</span>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Active Defense Action Card -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg border-0 p-5 text-white">
                <h3 class="font-bold text-lg mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    {{ __('messages.sec_active_defense_title') }}
                </h3>
                <p class="text-sm text-white/80 mb-5 leading-relaxed">
                    {{ __('messages.sec_active_defense_desc') }}
                </p>
                <button class="w-full bg-white text-indigo-600 hover:bg-indigo-50 font-bold py-2.5 rounded-lg transition-colors shadow-sm text-sm" onclick="alert('Tính năng phòng thủ tự động đang được phát triển!')">
                    {{ __('messages.sec_btn_setup_defense') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
