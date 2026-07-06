@extends('layouts.dashboard')
@section('title', __('messages.system_configuration'))

@section('content')
<div class="max-w-[1400px] mx-auto py-6 px-2 sm:px-4 lg:px-8" x-data="{ showSidebar: window.innerWidth >= 768 }" @resize.window="if(window.innerWidth < 768) showSidebar = false; else showSidebar = true;">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <button type="button" @click="showSidebar = !showSidebar" class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors text-gray-500 dark:text-gray-400" title="Bật/Tắt Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <svg class="w-6 h-6 text-accent-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ __('messages.system_configuration') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 sm:ml-12">{{ __('messages.system_config_desc') }}</p>
        </div>
        
        <!-- Scope Switcher -->
        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 p-1.5 sm:p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm w-full md:w-auto">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300 ml-2 hidden sm:inline">{{ __('messages.store_view') }}:</span>
            <select onchange="window.location.href='?section={{ $section }}&scope=' + this.value" class="flex-1 md:w-auto text-sm pl-3 pr-8 py-1.5 border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 font-medium cursor-pointer">
                @foreach($scopes as $s)
                    <option value="{{ $s['value'] }}" {{ $scopeParam === $s['value'] ? 'selected' : '' }}>{{ $s['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6 items-start relative">
        <!-- Left Sidebar (Tabs) -->
        <div id="settings-sidebar" x-show="showSidebar" style="display: none;" class="w-full md:w-64 flex-shrink-0 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm md:sticky md:top-6 md:max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar">
            <nav class="flex flex-col">
                @foreach($tree as $tabId => $tab)
                    @if(count($tab['sections']) > 0)
                    <div class="border-b border-gray-100 dark:border-gray-800" x-data="{ open: '{{ $activeTab === $tabId }}' === '1' }">
                        <button type="button" @click="open = !open" class="w-full px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center focus:outline-none">
                            {{ __($tab['label']) }}
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" style="display: none;" class="flex flex-col py-1 border-t border-gray-100 dark:border-gray-800">
                            @foreach($tab['sections'] as $secId => $sec)
                                <a href="?section={{ $secId }}&scope={{ $scopeParam }}" 
                                   class="block px-6 py-2.5 text-sm font-medium transition-colors border-l-4 break-words {{ $section === $secId ? 'text-accent-600 bg-accent-50 dark:bg-accent-500/10 border-accent-500' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800/50 border-transparent' }}">
                                    {{ __($sec['label']) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </nav>
        </div>

        <!-- Right Content Area -->
        <div class="flex-1 w-full min-w-0">
            @if($section === 'permissions')
                @include('pages.roles-permissions.index')
            @else
            <form action="{{ route('settings.save') }}" method="POST" id="config-form">
                @csrf
                <input type="hidden" name="section" value="{{ $section }}">
                <input type="hidden" name="scope_param" value="{{ $scopeParam }}">

                <div class="space-y-5">
                    @foreach($structure[$section]['groups'] as $groupId => $group)
                        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm overflow-hidden" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" class="w-full px-5 py-3.5 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800 focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-800/80 transition-colors">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __($group['label']) }}</h3>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <!-- Group Content -->
                            <div x-show="open" style="display: none;" class="p-4 sm:p-6 space-y-2">
                                @foreach($group['fields'] as $fieldId => $field)
                                    @php
                                        $path = "{$section}/{$groupId}/{$fieldId}";
                                        $val = $values[$path] ?? '';
                                        $isSystem = $useSystem[$path] ?? false;
                                        $inputName = "config[$path]";
                                        if ($field['type'] === 'multiselect') $inputName .= '[]';
                                        
                                        $disabledAttr = ($scope !== 'default' && $isSystem) ? 'disabled' : '';
                                        $opacityClass = ($scope !== 'default' && $isSystem) ? 'opacity-60 bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : 'bg-white dark:bg-gray-800';
                                    @endphp
                                    
                                    <div class="flex flex-col lg:flex-row gap-2 lg:gap-6 items-start py-4 border-b border-dashed border-gray-200 dark:border-gray-800 last:border-0 last:pb-0">
                                        <!-- Label & Scope -->
                                        <div class="w-full lg:w-1/3 flex flex-col justify-start lg:pt-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __($field['label']) }}
                                            </label>
                                            <span class="inline-block text-[10px] text-gray-500 dark:text-gray-400 mt-1 tracking-widest uppercase font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded w-max">
                                                [{{ $scope === 'default' ? 'Global' : ($scope === 'websites' ? 'Website' : 'Store') }}]
                                            </span>
                                        </div>
                                        
                                        <!-- Input Control -->
                                        <div class="w-full lg:w-2/3 flex flex-col sm:flex-row items-start gap-4">
                                            <div class="w-full sm:flex-1 min-w-0">
                                                @if($field['type'] === 'select')
                                                    <select name="{{ $inputName }}" id="input-{{ md5($path) }}" {{ $disabledAttr }} class="w-full max-w-md text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $opacityClass }} transition-colors">
                                                        @foreach($field['options'] as $optVal => $optLabel)
                                                            <option value="{{ $optVal }}" {{ $val == $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field['type'] === 'multiselect')
                                                    @php $valArr = explode(',', (string)$val); @endphp
                                                    <select name="{{ $inputName }}" id="input-{{ md5($path) }}" {{ $disabledAttr }} multiple size="5" class="w-full max-w-md text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $opacityClass }} transition-colors custom-scrollbar">
                                                        @foreach($field['options'] as $optVal => $optLabel)
                                                            <option value="{{ $optVal }}" {{ in_array($optVal, $valArr) ? 'selected' : '' }}>{{ $optLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="text-[11px] text-gray-500 mt-1">{{ __('messages.multiselect_hint') }}</p>
                                                @elseif($field['type'] === 'color')
                                                    <div class="flex items-center gap-2">
                                                        <input type="color" name="{{ $inputName }}" id="input-{{ md5($path) }}" value="{{ $val }}" {{ $disabledAttr }} class="h-9 w-14 p-0.5 border border-gray-300 dark:border-gray-700 rounded cursor-pointer {{ $opacityClass }}">
                                                        <input type="text" value="{{ $val }}" readonly class="w-24 text-sm px-2 py-1.5 border border-gray-300 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-500">
                                                    </div>
                                                @elseif($field['type'] === 'repeater')
                                                    @php
                                                        $rows = json_decode((string)$val, true);
                                                        if (!is_array($rows)) $rows = [];
                                                    @endphp
                                                    
                                                    @if($path === 'software_connections/products/list')
                                                    <div x-data="{
                                                        rows: {{ json_encode($rows ?: []) }},
                                                        errors: {},
                                                        addRow() {
                                                            this.rows.push({ name:'', host:'', port:'22', username:'', auth_type:'password', password:'', private_key:'', pem_filename:'' });
                                                        },
                                                        removeRow(i) {
                                                            this.rows.splice(i, 1);
                                                            this.errors = {};
                                                        },
                                                        validateAll() {
                                                            let e = {};
                                                            this.rows.forEach((row, i) => {
                                                                if (!row.name?.trim()) e[i+'_name'] = true;
                                                                if (!row.host?.trim()) e[i+'_host'] = true;
                                                                if (!row.port?.toString().trim()) e[i+'_port'] = true;
                                                                if (!row.username?.trim()) e[i+'_username'] = true;
                                                                const auth = row.auth_type || 'password';
                                                                if (auth === 'password' && !row.password?.trim()) e[i+'_password'] = true;
                                                                if ((auth === 'key_text' || auth === 'pem_file') && !row.private_key?.trim()) e[i+'_private_key'] = true;
                                                            });
                                                            this.errors = e;
                                                            return Object.keys(e).length === 0;
                                                        }
                                                    }" class="w-full" id="input-{{ md5($path) }}">

                                                        <div class="space-y-4">
                                                            <template x-for="(row, index) in rows" :key="index">
                                                                <div class="relative bg-gray-50 dark:bg-gray-900 border rounded-xl shadow-sm overflow-hidden group transition-all"
                                                                     :class="Object.keys(errors).some(k => k.startsWith(index+'_')) ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'">

                                                                    <!-- Card header -->
                                                                    <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                                                                            {{ __('Server') }} #<span x-text="index + 1"></span>
                                                                            <span x-show="row.name" x-text="'— ' + row.name" class="text-accent-500 font-medium normal-case tracking-normal"></span>
                                                                        </span>
                                                                        <button type="button" @click="removeRow(index)" {{ $disabledAttr }}
                                                                                class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors opacity-0 group-hover:opacity-100">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                        </button>
                                                                    </div>

                                                                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                        <!-- Product Name full-width -->
                                                                        <div class="sm:col-span-2 lg:col-span-3">
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Product Name') }} <span class="text-red-500">*</span></label>
                                                                            <input type="text" :name="`config[{{ $path }}][${index}][name]`" x-model="row.name"
                                                                                   placeholder="{{ __('e.g. ERP System, POS App') }}"
                                                                                   :class="errors[index+'_name'] ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'"
                                                                                   class="w-full text-sm px-3 py-2 border rounded-md text-gray-900 dark:text-white dark:bg-gray-900 focus:ring-accent-500 focus:border-accent-500 transition-colors">
                                                                            <p x-show="errors[index+'_name']" class="text-xs text-red-500 mt-1">{{ __('Product name is required') }}</p>
                                                                        </div>

                                                                        <!-- Host -->
                                                                        <div>
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Host / IP') }} <span class="text-red-500">*</span></label>
                                                                            <input type="text" :name="`config[{{ $path }}][${index}][host]`" x-model="row.host"
                                                                                   placeholder="192.168.1.10"
                                                                                   :class="errors[index+'_host'] ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'"
                                                                                   class="w-full text-sm px-3 py-2 border rounded-md text-gray-900 dark:text-white dark:bg-gray-900 focus:ring-accent-500 focus:border-accent-500 font-mono transition-colors">
                                                                            <p x-show="errors[index+'_host']" class="text-xs text-red-500 mt-1">{{ __('Host is required') }}</p>
                                                                        </div>

                                                                        <!-- Port -->
                                                                        <div>
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Port') }} <span class="text-red-500">*</span></label>
                                                                            <input type="number" :name="`config[{{ $path }}][${index}][port]`" x-model="row.port"
                                                                                   placeholder="22" min="1" max="65535"
                                                                                   :class="errors[index+'_port'] ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'"
                                                                                   class="w-full text-sm px-3 py-2 border rounded-md text-gray-900 dark:text-white dark:bg-gray-900 focus:ring-accent-500 focus:border-accent-500 font-mono transition-colors">
                                                                            <p x-show="errors[index+'_port']" class="text-xs text-red-500 mt-1">{{ __('Port is required') }}</p>
                                                                        </div>

                                                                        <!-- Username -->
                                                                        <div>
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Username') }} <span class="text-red-500">*</span></label>
                                                                            <input type="text" :name="`config[{{ $path }}][${index}][username]`" x-model="row.username"
                                                                                   placeholder="root"
                                                                                   :class="errors[index+'_username'] ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'"
                                                                                   class="w-full text-sm px-3 py-2 border rounded-md text-gray-900 dark:text-white dark:bg-gray-900 focus:ring-accent-500 focus:border-accent-500 font-mono transition-colors">
                                                                            <p x-show="errors[index+'_username']" class="text-xs text-red-500 mt-1">{{ __('Username is required') }}</p>
                                                                        </div>

                                                                        <!-- Auth method toggle -->
                                                                        <div class="sm:col-span-2 lg:col-span-3">
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">{{ __('Authentication Method') }}</label>
                                                                            <div class="flex flex-wrap gap-2">
                                                                                <template x-for="method in [{val:'password',icon:'key',label:'{{ __("Password") }}'},{val:'pem_file',icon:'clip',label:'{{ __("PEM / Key File") }}'},{val:'key_text',icon:'doc',label:'{{ __("Private Key (Text)") }}'}]" :key="method.val">
                                                                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-all text-sm"
                                                                                           :class="(row.auth_type||'password')===method.val ? 'border-accent-500 bg-accent-50 dark:bg-accent-500/10 text-accent-700 dark:text-accent-300 font-semibold' : 'border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                                                                                        <input type="radio" :name="`config[{{ $path }}][${index}][auth_type]`" :value="method.val" x-model="row.auth_type" class="hidden">
                                                                                        <span x-text="method.label"></span>
                                                                                    </label>
                                                                                </template>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Password -->
                                                                        <div class="sm:col-span-2 lg:col-span-3" x-show="(row.auth_type||'password')==='password'" style="display:none;">
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Password') }} <span class="text-red-500">*</span></label>
                                                                            <input type="password" :name="`config[{{ $path }}][${index}][password]`" x-model="row.password"
                                                                                   placeholder="••••••••"
                                                                                   :class="errors[index+'_password'] ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'"
                                                                                   class="w-full text-sm px-3 py-2 border rounded-md text-gray-900 dark:text-white dark:bg-gray-900 focus:ring-accent-500 focus:border-accent-500 transition-colors">
                                                                            <p x-show="errors[index+'_password']" class="text-xs text-red-500 mt-1">{{ __('Password is required') }}</p>
                                                                        </div>

                                                                        <!-- PEM file upload -->
                                                                        <div class="sm:col-span-2 lg:col-span-3" x-show="row.auth_type==='pem_file'" style="display:none;">
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('PEM / Key File') }} <span class="text-red-500">*</span></label>
                                                                            <input type="file" accept=".pem,.key,.ppk,.pub"
                                                                                   @change="const f=$event.target.files[0]; if(f){const r=new FileReader(); r.onload=e=>{row.private_key=e.target.result; row.pem_filename=f.name;}; r.readAsText(f);}"
                                                                                   class="w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-accent-50 file:text-accent-700 dark:file:bg-accent-500/10 dark:file:text-accent-300 border border-gray-300 dark:border-gray-700 rounded-md py-1.5 px-3 dark:bg-gray-900 dark:text-gray-300">
                                                                            <p x-show="row.pem_filename" class="text-xs text-accent-500 mt-1 font-mono" x-text="'✓ ' + row.pem_filename"></p>
                                                                            <p class="text-[11px] text-gray-400 mt-1">{{ __('Accepted: .pem, .key, .ppk') }}</p>
                                                                            <input type="hidden" :name="`config[{{ $path }}][${index}][private_key]`" x-model="row.private_key">
                                                                            <p x-show="errors[index+'_private_key']" class="text-xs text-red-500 mt-1">{{ __('Key file is required') }}</p>
                                                                        </div>

                                                                        <!-- Private Key text -->
                                                                        <div class="sm:col-span-2 lg:col-span-3" x-show="row.auth_type==='key_text'" style="display:none;">
                                                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ __('Private Key Content') }} <span class="text-red-500">*</span></label>
                                                                            <textarea :name="`config[{{ $path }}][${index}][private_key]`" x-model="row.private_key"
                                                                                      placeholder="-----BEGIN RSA PRIVATE KEY-----&#10;...&#10;-----END RSA PRIVATE KEY-----"
                                                                                      rows="5"
                                                                                      :class="errors[index+'_private_key'] ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'"
                                                                                      class="w-full text-xs px-3 py-2 border rounded-md text-gray-900 dark:text-white dark:bg-gray-900 focus:ring-accent-500 focus:border-accent-500 font-mono resize-y transition-colors"></textarea>
                                                                            <p x-show="errors[index+'_private_key']" class="text-xs text-red-500 mt-1">{{ __('Private key is required') }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <!-- Empty state -->
                                                        <div x-show="rows.length === 0" class="mt-2 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                                                            <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M12 5l7 7-7 7"/></svg>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No products connected yet.') }}</p>
                                                        </div>

                                                        <!-- Validation summary -->
                                                        <div x-show="Object.keys(errors).length > 0" class="mt-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-lg flex items-start gap-2">
                                                            <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                            <p class="text-sm text-red-600 dark:text-red-400">{{ __('Please fill in all required fields before saving.') }}</p>
                                                        </div>

                                                        <!-- Add button -->
                                                        <button type="button" @click="addRow()" {{ $disabledAttr }}
                                                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-accent-600 bg-accent-50 hover:bg-accent-100 dark:text-accent-300 dark:bg-accent-500/10 dark:hover:bg-accent-500/20 border border-accent-200 dark:border-accent-500/30 rounded-lg transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            {{ __('Add Product Connection') }}
                                                        </button>
                                                    </div>
                                                    @else
                                                    <!-- Generic Repeater Component -->
                                                    @php 
                                                        $cols = $field['columns'] ?? ['key' => 'Key', 'value' => 'Value']; 
                                                        $defaultRow = array_fill_keys(array_keys($cols), '');
                                                    @endphp
                                                    <div x-data="{
                                                        rows: {{ json_encode($rows ?: []) }},
                                                        defaultRow: {{ json_encode($defaultRow) }},
                                                        addRow() {
                                                            this.rows.push({...this.defaultRow});
                                                        },
                                                        removeRow(i) {
                                                            this.rows.splice(i, 1);
                                                        }
                                                    }" class="w-full">
                                                        <div class="space-y-3">
                                                            <template x-for="(row, index) in rows" :key="index">
                                                                <div class="flex items-start gap-4 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-{{ min(count($cols), 3) }} gap-4">
                                                                        @foreach($cols as $colKey => $colLabel)
                                                                            <div class="space-y-1">
                                                                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $colLabel }}</label>
                                                                                <input type="text" :name="`config[{{ $path }}][${index}][{{ $colKey }}]`" x-model="row.{{ $colKey }}" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 shadow-sm transition-colors">
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <button type="button" @click="removeRow(index)" class="mt-6 p-2 text-red-500 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-md transition-colors" title="{{ __('messages.delete_this_row') }}">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        
                                                        <div x-show="rows.length === 0" class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center bg-gray-50 dark:bg-gray-800/30">
                                                            <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data_configured') }}</p>
                                                        </div>
                                                        
                                                        <button type="button" @click="addRow()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-accent-600 bg-accent-50 hover:bg-accent-100 dark:text-accent-300 dark:bg-accent-500/10 dark:hover:bg-accent-500/20 border border-accent-200 dark:border-accent-500/30 rounded-lg transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            {{ __('messages.add_new_row') }}
                                                        </button>
                                                    </div>
                                                    @endif
                                                @else
                                                    <input type="{{ $field['type'] }}" name="{{ $inputName }}" id="input-{{ md5($path) }}" value="{{ $val }}" {{ $disabledAttr }} class="w-full max-w-md text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $opacityClass }} transition-colors">
                                                @endif
                                            </div>
                                            
                                            <!-- Use System Value Checkbox -->
                                            @if($scope !== 'default')
                                                <div class="flex-shrink-0 flex items-center gap-2 pt-2 sm:pt-0 sm:py-2">
                                                    <input type="checkbox" name="use_system[{{ $path }}]" id="use_system-{{ md5($path) }}" value="1" {{ $isSystem ? 'checked' : '' }} onchange="toggleField('{{ md5($path) }}', this.checked)" class="w-4 h-4 text-accent-600 border-gray-300 rounded focus:ring-accent-500 cursor-pointer">
                                                    <label for="use_system-{{ md5($path) }}" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none whitespace-nowrap">{{ __('messages.use_system_value') }}</label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                </div>

                <!-- Submit Button Sticky Bottom -->
                <div class="sticky bottom-6 mt-8 flex justify-end">
                    <button type="submit" onclick="return validateRepeaters(event)" class="inline-flex items-center gap-2 px-6 py-2.5 shadow-lg shadow-accent-500/30 text-sm font-semibold rounded-lg text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        {{ __('messages.save_config') }}
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for multiselect */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.5); border-radius: 20px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function toggleField(hash, isChecked) {
        const input = document.getElementById('input-' + hash);
        if (input) {
            input.disabled = isChecked;
            if (isChecked) {
                input.classList.add('opacity-60', 'bg-gray-100', 'dark:bg-gray-800', 'cursor-not-allowed');
                input.classList.remove('bg-white');
            } else {
                input.classList.remove('opacity-60', 'bg-gray-100', 'cursor-not-allowed');
                input.classList.add('bg-white');
            }
        }
    }

    // Validate all AlpineJS repeater components before form submit
    function validateRepeaters(event) {
        let valid = true;
        document.querySelectorAll('[x-data]').forEach(el => {
            const component = window.Alpine?.getComponent?.(el) ?? el._x_dataStack?.[0];
            if (component && typeof component.validateAll === 'function') {
                if (!component.validateAll()) {
                    valid = false;
                    // Scroll the first invalid card into view
                    setTimeout(() => {
                        const firstErr = el.querySelector('.border-red-400');
                        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 50);
                }
            }
        });
        if (!valid) {
            event.preventDefault();
            return false;
        }
        return true;
    }

</script>
@endsection
