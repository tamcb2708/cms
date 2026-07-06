@extends('layouts.dashboard')

@section('title', __('messages.schema_info'))

@section('content')
<div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900 p-6" x-data="schemaManager()">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent-500 to-purple-500 bg-clip-text text-transparent">⚡ {{ __('messages.db_tracker') }} - {{ __('messages.schema_info') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.connected_to') }} <strong>{{ $creds['dbname'] }} @ {{ $creds['host'] }}</strong></p>
        </div>
        
        <!-- View Toggle -->
        <div class="flex bg-gray-200 dark:bg-gray-700 p-1 rounded-lg">
            <button @click="viewMode = 'list'" :class="{'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': viewMode === 'list', 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-300 dark:hover:bg-gray-600': viewMode !== 'list'}" class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors">
                {{ __('messages.tables_list') }}
            </button>
            <button @click="viewMode = 'erd'; loadErd()" :class="{'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': viewMode === 'erd', 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-300 dark:hover:bg-gray-600': viewMode !== 'erd'}" class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors">
                {{ __('messages.erd_diagram') }}
            </button>
        </div>
    </div>

    <!-- LIST VIEW (ACCORDION) -->
    <div x-show="viewMode === 'list'" class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
            <h3 class="font-medium">{{ __('messages.tables_list') }}</h3>
            <span class="text-xs font-medium bg-accent-100 text-accent-700 dark:bg-accent-500/20 dark:text-accent-300 px-2.5 py-1 rounded-full">{{ count($tables) }} Tables</span>
        </div>
        <div class="overflow-y-auto flex-1 p-4">
            <div class="flex flex-col gap-3">
                @foreach($tables as $table)
                <div x-data="{ expanded: false, loaded: false, loading: false, details: null }" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-colors hover:border-accent-400">
                    <button @click="expanded = !expanded; if(expanded && !loaded) { loading = true; fetch('{{ url('/db-tracker/schema') }}/{{ $table['table_name'] }}/details').then(r=>r.json()).then(d=>{details=d; loaded=true; loading=false}) }" class="w-full flex items-center justify-between p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors focus:outline-none">
                        <div class="flex items-center gap-2 text-gray-900 dark:text-white font-medium">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            {{ $table['table_name'] }}
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    
                    <div x-show="expanded" x-collapse>
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <!-- Loading State -->
                            <div x-show="loading" class="py-6 text-center text-gray-500">
                                <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-accent-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ __('messages.loading_info') }}
                            </div>
                            
                            <!-- Content State -->
                            <div x-show="loaded && details">
                                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ __('messages.data_columns') }}</h4>
                                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg mb-4">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-900/50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.column_name') }}</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.data_type') }}</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.is_required') }}</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.default_value') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                            <template x-for="col in details.columns" :key="col.column_name">
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium" :class="details.pks.includes(col.column_name) ? 'text-accent-600 dark:text-accent-400' : 'text-gray-900 dark:text-white'">
                                                        <span x-text="col.column_name"></span>
                                                        <span x-show="details.pks.includes(col.column_name)" class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400" title="Primary Key">PK</span>
                                                        <span x-show="details.fks && details.fks.some(fk => fk.column_name === col.column_name)" class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400" title="Foreign Key">FK</span>
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="col.data_type"></td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        <span x-show="col.is_nullable === 'NO'" class="text-red-500 font-bold">Yes</span>
                                                        <span x-show="col.is_nullable !== 'NO'">No</span>
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="col.column_default || 'NULL'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <template x-if="details.fks && details.fks.length > 0">
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ __('messages.foreign_keys') }}</h4>
                                        <ul class="space-y-1.5">
                                            <template x-for="fk in details.fks" :key="fk.column_name + fk.foreign_table_name">
                                                <li class="bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 px-3 py-1.5 rounded-md text-xs flex items-center gap-2 border border-blue-200 dark:border-blue-500/20">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    <span x-html="'{{ __('messages.col_reference', ['col' => '<strong>\' + fk.column_name + \'</strong>', 'table' => '<strong>\' + fk.foreign_table_name + \'</strong>', 'ref_col' => '<span>\' + fk.foreign_column_name + \'</span>']) }}'"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ERD VIEW -->
    <div x-show="viewMode === 'erd'" style="display: none;" class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
            <h3 class="font-medium">{{ __('messages.erd_title') }}</h3>
        </div>
        <div class="overflow-auto flex-1 p-4 bg-gray-50 dark:bg-gray-900/50 flex justify-center items-start">
            <div id="erd-loading" x-show="isErdLoading" class="p-8 text-center text-gray-500">
                <svg class="animate-spin h-8 w-8 mx-auto mb-4 text-accent-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                {{ __('messages.building_erd') }}
            </div>
            <div id="erd-container" class="mermaid w-full max-w-full text-center" x-show="!isErdLoading">
                <!-- Mermaid ERD will be rendered here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.6.1/dist/svg-pan-zoom.min.js"></script>
<script>
    function registerSchemaManager() {
        Alpine.data('schemaManager', () => ({
            viewMode: 'list', // 'list' or 'erd'
            isErdLoading: false,
            erdLoaded: false,
            panZoomInstance: null,

            init() {
                mermaid.initialize({ startOnLoad: false, theme: document.documentElement.classList.contains('dark') ? 'dark' : 'default' });
                
                // Lắng nghe sự kiện chuyển đổi Dark/Light mode của hệ thống
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'class') {
                            const isDark = document.documentElement.classList.contains('dark');
                            mermaid.initialize({ theme: isDark ? 'dark' : 'default' });
                            
                            // Nếu đang ở màn hình ERD, vẽ lại để áp dụng màu mới
                            if (this.viewMode === 'erd' && this.erdLoaded) {
                                this.erdLoaded = false;
                                // Destroy old panZoom before re-rendering
                                if (this.panZoomInstance) {
                                    this.panZoomInstance.destroy();
                                    this.panZoomInstance = null;
                                }
                                this.loadErd(true);
                            }
                        }
                    });
                });
                observer.observe(document.documentElement, { attributes: true });
            },

            loadErd(force = false) {
                if(this.erdLoaded && !force) return;
                this.isErdLoading = true;
                
                fetch(`{{ route('db-tracker.schema.erd') }}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            // Show container first so SVG bounds can be calculated (prevents NaN error)
                            this.isErdLoading = false;
                            
                            setTimeout(() => {
                                const container = document.getElementById('erd-container');
                                container.innerHTML = data.mermaid;
                                container.removeAttribute('data-processed');
                                
                                mermaid.run({
                                    nodes: [container]
                                }).then(() => {
                                    this.erdLoaded = true;
                                    
                                    // Init pan and zoom
                                    setTimeout(() => {
                                        const svg = container.querySelector('svg');
                                        if (svg) {
                                            svg.style.width = '100%';
                                            svg.style.height = '600px';
                                            this.panZoomInstance = svgPanZoom(svg, {
                                                zoomEnabled: true,
                                                controlIconsEnabled: true,
                                                fit: true,
                                                center: true,
                                                minZoom: 0.1
                                            });
                                        }
                                    }, 100);
                                });
                            }, 50);
                        } else {
                            this.isErdLoading = false;
                            window.showToast("{{ __('messages.error_title') }}", data.message, "error");
                            this.viewMode = 'list';
                        }
                    })
                    .catch(err => {
                        this.isErdLoading = false;
                        window.showToast("{{ __('messages.error_title') }}", "{{ __('messages.cannot_load_erd') }}", "error");
                        this.viewMode = 'list';
                    });
            }
        }));
    }

    if (window.Alpine) {
        registerSchemaManager();
    } else {
        document.addEventListener('alpine:init', registerSchemaManager);
    }
</script>
@endpush
