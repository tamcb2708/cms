<script>
    let { growth = [] } = $props();

    const w = 480, h = 140, padL = 6, padR = 6, padT = 12, padB = 22;
    const innerW = w - padL - padR, innerH = h - padT - padB;

    function xAt(i, count) {
        return padL + (innerW * i) / Math.max(1, count - 1);
    }

    function yAt(v, hi) {
        return padT + innerH - (v / hi) * innerH;
    }

    const chart = $derived.by(() => {
        if (!growth.length) return null;

        const values = growth.map((p) => p.value);
        const max = Math.max(1, ...values);
        const hi = max * 1.2;
        const count = growth.length;

        const coords = growth.map((p, i) => [xAt(i, count), yAt(p.value, hi)]);
        const linePath = coords.map((c, i) => `${i === 0 ? 'M' : 'L'}${c[0].toFixed(1)},${c[1].toFixed(1)}`).join(' ');
        const areaPath = `${linePath} L${xAt(count - 1, count).toFixed(1)},${padT + innerH} L${xAt(0, count).toFixed(1)},${padT + innerH} Z`;

        const gridLines = [0, 1, 2].map((g) => padT + (innerH * g) / 2);
        const last = coords[coords.length - 1];

        return { coords, linePath, areaPath, gridLines, last, labels: growth.map((p, i) => ({ x: xAt(i, count), label: p.label })) };
    });
</script>

<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Tenant growth</h2>
    <p class="mt-0.5 text-xs text-gray-400">New tenants over the last 7 days</p>

    <div class="relative mt-4">
        {#if chart}
            <svg viewBox="0 0 {w} {h}" width="100%" height={h} preserveAspectRatio="none">
                <defs>
                    <linearGradient id="tenant-growth-gradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#0f6e6a" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#0f6e6a" stop-opacity="0" />
                    </linearGradient>
                </defs>
                {#each chart.gridLines as gy}
                    <line x1={padL} y1={gy.toFixed(1)} x2={w - padR} y2={gy.toFixed(1)} stroke="currentColor" class="text-gray-100 dark:text-white/10" stroke-width="1" />
                {/each}
                <path d={chart.areaPath} fill="url(#tenant-growth-gradient)" stroke="none" />
                <path d={chart.linePath} fill="none" stroke="#0f6e6a" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                <circle cx={chart.last[0].toFixed(1)} cy={chart.last[1].toFixed(1)} r="4" fill="#0f6e6a" />
                {#each chart.labels as l}
                    <text x={l.x.toFixed(1)} y={h - 4} text-anchor="middle" fill="currentColor" class="text-gray-400" font-size="9">{l.label}</text>
                {/each}
            </svg>
        {/if}
    </div>
</div>
