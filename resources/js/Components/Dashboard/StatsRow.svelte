<script>
    let { overview } = $props();

    const currency = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 });
    const number = new Intl.NumberFormat('vi-VN');

    function formatChange(changePercent) {
        const sign = changePercent > 0 ? '+' : '';
        return `${sign}${changePercent}% so với tháng trước`;
    }

    const tiles = $derived([
        { label: 'Doanh thu tháng này', value: currency.format(overview.revenue.value), change: overview.revenue.changePercent, border: 'border-t-indigo-500' },
        { label: 'Gói đang hoạt động', value: number.format(overview.activeSubscriptions.value), change: overview.activeSubscriptions.changePercent, border: 'border-t-green-500' },
        { label: 'Tenant mới', value: number.format(overview.newTenants.value), change: overview.newTenants.changePercent, border: 'border-t-yellow-500' },
        { label: 'Tổng khách hàng', value: number.format(overview.totalCustomers.value), change: overview.totalCustomers.changePercent, border: 'border-t-purple-500' },
    ]);
</script>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    {#each tiles as tile}
        <div class="rounded-xl border border-t-4 border-gray-100 {tile.border} bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{tile.label}</div>
            <div class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{tile.value}</div>
            <div class="mt-1 text-xs font-medium {tile.change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                {formatChange(tile.change)}
            </div>
        </div>
    {/each}
</div>
