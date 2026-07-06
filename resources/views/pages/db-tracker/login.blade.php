<div class="flex items-center justify-center h-full p-6">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('messages.db_tracker') }} ({{ __('messages.instance') }} {{ $tab }})</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.connect_postgres_desc') }}</p>
        </div>
        
        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="form-login-{{ $tab }}" onsubmit="connectDbTracker(event, {{ $tab }})" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Host</label>
                    <input type="text" name="host" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-accent-500 focus:border-accent-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port</label>
                    <input type="number" name="port" required value="5432" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-accent-500 focus:border-accent-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.database_name') }}</label>
                    <input type="text" name="dbname" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-accent-500 focus:border-accent-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.username') }}</label>
                    <input type="text" name="user" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-accent-500 focus:border-accent-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.password') }}</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-accent-500 focus:border-accent-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                        {{ __('messages.connect') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function connectDbTracker(e, tabId) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const origText = btn.innerHTML;
        btn.innerHTML = 'Đang kết nối...';
        btn.disabled = true;

        const formData = new FormData(form);
        formData.append('tab', tabId);

        fetch(`{{ route('db-tracker.connect') }}`, {
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
                // Reload tab
                loadedTabs[tabId] = false;
                loadTab(tabId);
            } else {
                alert(data.message || 'Lỗi kết nối');
                btn.innerHTML = origText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert('Có lỗi xảy ra!');
            btn.innerHTML = origText;
            btn.disabled = false;
        });
    }
</script>
