<script>
    import { onMount, onDestroy } from 'svelte';
    import mermaid from 'mermaid';
    import svgPanZoom from 'svg-pan-zoom';
    import { route } from '../../lib/route.js';
    import { addToast } from '../../stores/toast.svelte.js';

    let isLoading = $state(false);
    let loaded = $state(false);
    let container = $state(null);
    let panZoomInstance = null;
    let observer;

    function currentTheme() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'default';
    }

    async function load(force = false) {
        if (loaded && !force) return;
        isLoading = true;

        try {
            const res = await fetch(route('db-tracker.schema.erd'));
            const data = await res.json();

            if (!data.success) {
                isLoading = false;
                addToast({ title: 'Lỗi', message: data.message, type: 'error' });
                return;
            }

            isLoading = false;
            await new Promise((r) => setTimeout(r, 50));

            container.innerHTML = data.mermaid;
            container.removeAttribute('data-processed');
            await mermaid.run({ nodes: [container] });
            loaded = true;

            await new Promise((r) => setTimeout(r, 100));
            const svg = container.querySelector('svg');
            if (svg) {
                svg.style.width = '100%';
                svg.style.height = '600px';
                panZoomInstance = svgPanZoom(svg, {
                    zoomEnabled: true,
                    controlIconsEnabled: true,
                    fit: true,
                    center: true,
                    minZoom: 0.1,
                });
            }
        } catch (err) {
            isLoading = false;
            addToast({ title: 'Lỗi', message: 'Không thể tải sơ đồ ERD', type: 'error' });
        }
    }

    function redraw() {
        loaded = false;
        if (panZoomInstance) {
            panZoomInstance.destroy();
            panZoomInstance = null;
        }
        load(true);
    }

    onMount(() => {
        mermaid.initialize({ startOnLoad: false, theme: currentTheme() });
        load();

        observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    mermaid.initialize({ theme: currentTheme() });
                    if (loaded) redraw();
                }
            }
        });
        observer.observe(document.documentElement, { attributes: true });
    });

    onDestroy(() => {
        observer?.disconnect();
        panZoomInstance?.destroy();
    });
</script>

{#if isLoading}
    <div class="p-8 text-center text-gray-500">
        <svg class="mx-auto mb-4 h-8 w-8 animate-spin text-accent-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        Đang xây dựng sơ đồ ERD...
    </div>
{/if}
<div bind:this={container} class="mermaid w-full max-w-full text-center" class:hidden={isLoading}></div>
