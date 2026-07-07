<script>
    let { rows = [], disabled = false, onChange, errors = {} } = $props();

    const AUTH_METHODS = [
        { val: 'password', label: 'Password' },
        { val: 'pem_file', label: 'PEM / Key File' },
        { val: 'key_text', label: 'Private Key (Text)' },
    ];

    function update(index, patch) {
        const next = rows.map((row, i) => (i === index ? { ...row, ...patch } : row));
        onChange(next);
    }

    function addRow() {
        onChange([...rows, { name: '', host: '', port: '22', username: '', auth_type: 'password', password: '', private_key: '', pem_filename: '' }]);
    }

    function removeRow(index) {
        onChange(rows.filter((_, i) => i !== index));
    }

    function hasError(index, field) {
        return Boolean(errors[`${index}_${field}`]);
    }

    async function onPemFile(index, event) {
        const file = event.target.files[0];
        if (!file) return;
        const text = await file.text();
        update(index, { private_key: text, pem_filename: file.name });
    }
</script>

<div class="w-full">
    <div class="space-y-4">
        {#each rows as row, index}
            <div class="group relative overflow-hidden rounded-xl border bg-gray-50 shadow-sm transition-all dark:bg-gray-900 {Object.keys(errors).some((k) => k.startsWith(index + '_')) ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700'}">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800/50">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                        Server #{index + 1}
                        {#if row.name}<span class="font-medium normal-case tracking-normal text-accent-500">— {row.name}</span>{/if}
                    </span>
                    <button type="button" onclick={() => removeRow(index)} {disabled} class="rounded-md p-1.5 text-red-400 opacity-0 transition-colors hover:bg-red-50 hover:text-red-600 group-hover:opacity-100 dark:hover:bg-red-900/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" value={row.name} oninput={(e) => update(index, { name: e.target.value })} placeholder="e.g. ERP System, POS App"
                            class="w-full rounded-md border px-3 py-2 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:bg-gray-900 dark:text-white {hasError(index, 'name') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'}" />
                        {#if hasError(index, 'name')}<p class="mt-1 text-xs text-red-500">Product name is required</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Host / IP <span class="text-red-500">*</span></label>
                        <input type="text" value={row.host} oninput={(e) => update(index, { host: e.target.value })} placeholder="192.168.1.10"
                            class="w-full rounded-md border px-3 py-2 font-mono text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:bg-gray-900 dark:text-white {hasError(index, 'host') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'}" />
                        {#if hasError(index, 'host')}<p class="mt-1 text-xs text-red-500">Host is required</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Port <span class="text-red-500">*</span></label>
                        <input type="number" value={row.port} oninput={(e) => update(index, { port: e.target.value })} placeholder="22" min="1" max="65535"
                            class="w-full rounded-md border px-3 py-2 font-mono text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:bg-gray-900 dark:text-white {hasError(index, 'port') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'}" />
                        {#if hasError(index, 'port')}<p class="mt-1 text-xs text-red-500">Port is required</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Username <span class="text-red-500">*</span></label>
                        <input type="text" value={row.username} oninput={(e) => update(index, { username: e.target.value })} placeholder="root"
                            class="w-full rounded-md border px-3 py-2 font-mono text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:bg-gray-900 dark:text-white {hasError(index, 'username') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'}" />
                        {#if hasError(index, 'username')}<p class="mt-1 text-xs text-red-500">Username is required</p>{/if}
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-xs font-semibold text-gray-600 dark:text-gray-300">Authentication Method</label>
                        <div class="flex flex-wrap gap-2">
                            {#each AUTH_METHODS as method}
                                <label class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-all {((row.auth_type || 'password') === method.val) ? 'border-accent-500 bg-accent-50 font-semibold text-accent-700 dark:bg-accent-500/10 dark:text-accent-300' : 'border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800/50'}">
                                    <input type="radio" checked={(row.auth_type || 'password') === method.val} onchange={() => update(index, { auth_type: method.val })} class="hidden" />
                                    {method.label}
                                </label>
                            {/each}
                        </div>
                    </div>

                    {#if (row.auth_type || 'password') === 'password'}
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                            <input type="password" value={row.password} oninput={(e) => update(index, { password: e.target.value })} placeholder="••••••••"
                                class="w-full rounded-md border px-3 py-2 text-sm text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:bg-gray-900 dark:text-white {hasError(index, 'password') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'}" />
                            {#if hasError(index, 'password')}<p class="mt-1 text-xs text-red-500">Password is required</p>{/if}
                        </div>
                    {:else if row.auth_type === 'pem_file'}
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">PEM / Key File <span class="text-red-500">*</span></label>
                            <input type="file" accept=".pem,.key,.ppk,.pub" onchange={(e) => onPemFile(index, e)}
                                class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-accent-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-accent-700 dark:border-gray-700 dark:text-gray-300 dark:file:bg-accent-500/10 dark:file:text-accent-300" />
                            {#if row.pem_filename}<p class="mt-1 font-mono text-xs text-accent-500">✓ {row.pem_filename}</p>{/if}
                            <p class="mt-1 text-[11px] text-gray-400">Accepted: .pem, .key, .ppk</p>
                            {#if hasError(index, 'private_key')}<p class="mt-1 text-xs text-red-500">Key file is required</p>{/if}
                        </div>
                    {:else if row.auth_type === 'key_text'}
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Private Key Content <span class="text-red-500">*</span></label>
                            <textarea value={row.private_key} oninput={(e) => update(index, { private_key: e.target.value })} rows="5" placeholder={"-----BEGIN RSA PRIVATE KEY-----\n...\n-----END RSA PRIVATE KEY-----"}
                                class="w-full resize-y rounded-md border px-3 py-2 font-mono text-xs text-gray-900 transition-colors focus:border-accent-500 focus:ring-accent-500 dark:bg-gray-900 dark:text-white {hasError(index, 'private_key') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-700'}"></textarea>
                            {#if hasError(index, 'private_key')}<p class="mt-1 text-xs text-red-500">Private key is required</p>{/if}
                        </div>
                    {/if}
                </div>
            </div>
        {/each}
    </div>

    {#if rows.length === 0}
        <div class="mt-2 rounded-xl border-2 border-dashed border-gray-200 p-8 text-center dark:border-gray-700">
            <svg class="mx-auto mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M12 5l7 7-7 7"/></svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">No products connected yet.</p>
        </div>
    {/if}

    {#if Object.keys(errors).length > 0}
        <div class="mt-3 flex items-start gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-3 dark:border-red-700 dark:bg-red-900/20">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-red-600 dark:text-red-400">Please fill in all required fields before saving.</p>
        </div>
    {/if}

    <button type="button" onclick={addRow} {disabled} class="mt-4 inline-flex items-center gap-2 rounded-lg border border-accent-200 bg-accent-50 px-4 py-2 text-sm font-semibold text-accent-600 transition-colors hover:bg-accent-100 dark:border-accent-500/30 dark:bg-accent-500/10 dark:text-accent-300 dark:hover:bg-accent-500/20">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product Connection
    </button>
</div>
