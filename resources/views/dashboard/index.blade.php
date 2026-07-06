@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="mx-auto flex max-w-6xl flex-col gap-6">
        @include('dashboard.partials.stats')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('dashboard.partials.growth-chart')
            @include('dashboard.partials.status-chart')
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('dashboard.partials.recent-tenants')
            @include('dashboard.partials.team-access')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function buildLineChart(container) {
                var points = JSON.parse(container.dataset.points || '[]');
                if (!points.length) return;

                var w = 480, h = 140, padL = 6, padR = 6, padT = 12, padB = 22;
                var innerW = w - padL - padR, innerH = h - padT - padB;
                var values = points.map(function (p) { return p.value; });
                var max = Math.max(1, Math.max.apply(null, values));
                var lo = 0, hi = max * 1.2;

                function xAt(i) { return padL + (innerW * i) / Math.max(1, points.length - 1); }
                function yAt(v) { return padT + innerH - ((v - lo) / (hi - lo)) * innerH; }

                var coords = points.map(function (p, i) { return [xAt(i), yAt(p.value)]; });
                var linePath = coords.map(function (c, i) { return (i === 0 ? 'M' : 'L') + c[0].toFixed(1) + ',' + c[1].toFixed(1); }).join(' ');
                var areaPath = linePath + ' L' + xAt(points.length - 1).toFixed(1) + ',' + (padT + innerH) + ' L' + xAt(0).toFixed(1) + ',' + (padT + innerH) + ' Z';

                var grid = '';
                for (var g = 0; g < 3; g++) {
                    var gy = padT + (innerH * g) / 2;
                    grid += '<line x1="' + padL + '" y1="' + gy.toFixed(1) + '" x2="' + (w - padR) + '" y2="' + gy.toFixed(1) + '" stroke="currentColor" class="text-gray-100 dark:text-white/10" stroke-width="1" />';
                }

                var labels = points.map(function (p, i) {
                    return '<text x="' + xAt(i).toFixed(1) + '" y="' + (h - 4) + '" text-anchor="middle" fill="currentColor" class="text-gray-400" font-size="9">' + p.label + '</text>';
                }).join('');

                var last = coords[coords.length - 1];

                container.innerHTML =
                    '<svg viewBox="0 0 ' + w + ' ' + h + '" width="100%" height="' + h + '" preserveAspectRatio="none">' +
                    '<defs><linearGradient id="lg-' + container.id + '" x1="0" y1="0" x2="0" y2="1">' +
                    '<stop offset="0%" stop-color="#0f6e6a" stop-opacity="0.25"/>' +
                    '<stop offset="100%" stop-color="#0f6e6a" stop-opacity="0"/>' +
                    '</linearGradient></defs>' +
                    grid +
                    '<path d="' + areaPath + '" fill="url(#lg-' + container.id + ')" stroke="none"/>' +
                    '<path d="' + linePath + '" fill="none" stroke="#0f6e6a" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>' +
                    '<circle cx="' + last[0].toFixed(1) + '" cy="' + last[1].toFixed(1) + '" r="4" fill="#0f6e6a"/>' +
                    labels +
                    '</svg>';
            }

            function buildDonut(container) {
                var counts = JSON.parse(container.dataset.counts || '{}');
                var entries = [
                    { key: 'active', color: '#22c55e', value: counts.active || 0 },
                    { key: 'pending', color: '#eab308', value: counts.pending || 0 },
                    { key: 'suspended', color: '#ef4444', value: counts.suspended || 0 },
                ];
                var total = entries.reduce(function (s, e) { return s + e.value; }, 0);
                var size = 120, cx = size / 2, cy = size / 2, r = 42, stroke = 16;
                var circumference = 2 * Math.PI * r;
                var offset = 0;

                var arcs = '<circle cx="' + cx + '" cy="' + cy + '" r="' + r + '" fill="none" stroke="currentColor" class="text-gray-100 dark:text-white/10" stroke-width="' + stroke + '" />';

                if (total > 0) {
                    arcs += entries.map(function (e) {
                        var frac = e.value / total;
                        var dash = frac * circumference;
                        var el = '<circle cx="' + cx + '" cy="' + cy + '" r="' + r + '" fill="none" stroke="' + e.color + '" stroke-width="' + stroke + '" ' +
                            'stroke-dasharray="' + Math.max(dash - 1.5, 0) + ' ' + (circumference - dash + 1.5) + '" ' +
                            'stroke-dashoffset="' + (-offset).toFixed(2) + '" transform="rotate(-90 ' + cx + ' ' + cy + ')" stroke-linecap="round"/>';
                        offset += dash;
                        return el;
                    }).join('');
                }

                container.innerHTML =
                    '<svg viewBox="0 0 ' + size + ' ' + size + '" width="' + size + '" height="' + size + '">' + arcs + '</svg>' +
                    '<div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">' +
                    '<span class="text-lg font-bold text-gray-950 dark:text-white">' + total + '</span>' +
                    '<span class="text-[9px] text-gray-400">tenants</span>' +
                    '</div>';
            }

            var lineEl = document.getElementById('tenant-growth-chart');
            var donutEl = document.getElementById('tenant-status-chart');
            if (lineEl) buildLineChart(lineEl);
            if (donutEl) buildDonut(donutEl);
        })();
    </script>
@endpush
