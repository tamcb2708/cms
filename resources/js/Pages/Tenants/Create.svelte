<script>
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import TenantForm from '../../Components/Tenants/TenantForm.svelte';
    import { route } from '../../lib/route.js';

    const form = useForm({
        name: '',
        domain: '',
        database_name: '',
        status: 'pending',
    });
</script>

<AppLayout title="Thêm Tenant">
    <div class="mx-auto max-w-2xl py-6">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Thêm tenant mới</h1>
            <a href={route('tenants.index')} class="flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>

        <TenantForm {form} mode="create" />
    </div>
</AppLayout>
