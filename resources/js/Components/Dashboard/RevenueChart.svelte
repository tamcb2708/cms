<script>
    import { onMount, onDestroy } from 'svelte';
    import { router } from '@inertiajs/svelte';
    import { Chart } from 'chart.js/auto';

    let { revenue, products = [], filters } = $props();

    let canvas = $state(null);
    let chart;
    let granularity = $state(filters.granularity);
    let product = $state(filters.product ?? '');
    let billingCycle = $state(filters.billing_cycle ?? '');

    const currency = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 });

    function applyFilters() {
        router.get(
            route('dashboard'),
            {
                granularity,
                product: product || undefined,
                billing_cycle: billingCycle || undefined,
            },
            { preserveState: true, preserveScroll: true, replace: true, only: ['revenue', 'revenueFilters'] },
        );
    }

    onMount(() => {
        chart = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: revenue.buckets.map((b) => b.label),
                datasets: [
                    {
                        label: 'Doanh thu',
                        data: revenue.buckets.map((b) => b.value),
                        backgroundColor: '#0f6e6a',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => currency.format(ctx.parsed.y) } },
                },
                scales: {
                    y: { ticks: { callback: (value) => currency.format(value) } },
                },
            },
        });
    });

    $effect(() => {
        if (chart) {
            chart.data.labels = revenue.buckets.map((b) => b.label);
            chart.data.datasets[0].data = revenue.buckets.map((b) => b.value);
            chart.update();
        }
    });

    onDestroy(() => chart?.destroy());
</script>

<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Doanh thu theo thời gian</h2>
            <p class="mt-0.5 text-xs text-gray-400">Tổng: {currency.format(revenue.total)}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <select bind:value={granularity} onchange={applyFilters} class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs dark:border-white/10 dark:bg-gray-800 dark:text-white">
                <option value="day">Theo ngày</option>
                <option value="week">Theo tuần</option>
                <option value="month">Theo tháng</option>
            </select>

            <select bind:value={product} onchange={applyFilters} class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs dark:border-white/10 dark:bg-gray-800 dark:text-white">
                <option value="">Tất cả sản phẩm</option>
                {#each products as p}
                    <option value={p.code}>{p.name}</option>
                {/each}
            </select>

            <select bind:value={billingCycle} onchange={applyFilters} class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs dark:border-white/10 dark:bg-gray-800 dark:text-white">
                <option value="">Tất cả loại gói</option>
                <option value="monthly">Theo tháng</option>
                <option value="yearly">Theo năm</option>
            </select>
        </div>
    </div>

    <div class="relative mt-4 h-64">
        <canvas bind:this={canvas}></canvas>
    </div>
</div>
