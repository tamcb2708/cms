<script>
    import { onMount, onDestroy } from 'svelte';
    import { Chart } from 'chart.js/auto';

    let { active = 0, idle = 0, other = 0 } = $props();

    let canvas = $state(null);
    let chart;

    onMount(() => {
        chart = new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Idle', 'Other'],
                datasets: [{
                    data: [active, idle, other],
                    backgroundColor: ['#10B981', '#6B7280', '#F59E0B'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151' },
                    },
                },
            },
        });
    });

    $effect(() => {
        if (chart) {
            chart.data.datasets[0].data = [active, idle, other];
            chart.update();
        }
    });

    onDestroy(() => chart?.destroy());
</script>

<canvas bind:this={canvas}></canvas>
