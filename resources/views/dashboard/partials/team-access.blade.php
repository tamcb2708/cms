@php
    $metrics = [
        ['label' => 'Admin users', 'value' => $team['users']],
        ['label' => 'Roles', 'value' => $team['roles']],
        ['label' => 'Super Admin', 'value' => $team['super_admins']],
        ['label' => 'Tenant Admin', 'value' => $team['tenant_admins']],
    ];
@endphp

<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
    <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Team &amp; access</h2>

    <div class="mt-4 grid grid-cols-2 gap-4">
        @foreach ($metrics as $metric)
            <div>
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</div>
                <div class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">{{ $metric['value'] }}</div>
            </div>
        @endforeach
    </div>
</div>
