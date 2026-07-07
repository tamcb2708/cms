<script>
    let { title, creds, children } = $props();
</script>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="bg-gradient-to-r from-accent-500 to-purple-500 bg-clip-text text-xl font-bold text-transparent">
            ⚡ DB Tracker - {title}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Đang kết nối tới <strong>{creds.dbname} @ {creds.host}</strong>
        </p>
    </div>
    {#if children}
        <div class="flex items-center gap-3">
            {@render children()}
        </div>
    {/if}
</div>
