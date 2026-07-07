<script>
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import AdminForm from '../../Components/AdminDirectory/AdminForm.svelte';
    import { route } from '../../lib/route.js';

    let { admin, roles = [] } = $props();

    const form = useForm({
        username: admin.username,
        name: admin.name,
        email: admin.email,
        password: '',
        password_confirmation: '',
        valid_from: admin.valid_from ?? '',
        valid_until: admin.valid_until ?? '',
        role: admin.role ?? '',
    });
</script>

<AppLayout title="Sửa quản trị viên">
    <div class="mx-auto max-w-5xl py-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="flex items-center gap-3 text-2xl font-bold text-gray-900 dark:text-white">
                    <div class="rounded-lg bg-accent-100 p-2 text-accent-600 dark:bg-accent-500/20 dark:text-accent-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    Sửa quản trị viên
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cập nhật thông tin và quyền truy cập của {admin.name}.</p>
            </div>
            <a href={route('admin-directory.index')} class="flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>

        <AdminForm {form} {roles} mode="edit" adminId={admin.id} />
    </div>
</AppLayout>
