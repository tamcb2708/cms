<script>
    import { toasts, removeToast } from '../stores/toast.svelte.js';
</script>

<div
    class="pointer-events-none fixed top-6 left-1/2 z-[100] flex w-full max-w-md -translate-x-1/2 flex-col items-center gap-2 px-4"
>
    {#each toasts as toast (toast.id)}
        <div
            class="pointer-events-auto flex w-full items-center overflow-hidden rounded-xl border border-gray-100 bg-white p-4 shadow-2xl ring-1 ring-black/5 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800 {toast.type === 'success' ? 'shadow-green-500/20' : toast.type === 'error' ? 'shadow-red-500/20' : 'shadow-blue-500/20'}"
        >
            <div class="flex-shrink-0">
                {#if toast.type === 'success'}
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                {:else if toast.type === 'error'}
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                {:else}
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                {/if}
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{toast.title}</p>
                {#if toast.message}
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{toast.message}</p>
                {/if}
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button
                    type="button"
                    onclick={() => removeToast(toast.id)}
                    class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:bg-gray-800"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </div>
    {/each}
</div>
