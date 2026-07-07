<script>
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import TenantForm from '../../Components/Tenants/TenantForm.svelte';
    import { route } from '../../lib/route.js';

    let { tenant } = $props();

    const form = useForm({
        name: tenant.name,
        domain: tenant.domain,
        database_name: tenant.database_name ?? '',
        status: tenant.status,
    });
</script>

<AppLayout title="Sửa Tenant">
    <div class="mx-auto max-w-2xl py-6">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sửa tenant</h1>
            <a href={route('tenants.index')} class="flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>

        <TenantForm {form} mode="edit" tenantId={tenant.id} />
    </div>
</AppLayout>
