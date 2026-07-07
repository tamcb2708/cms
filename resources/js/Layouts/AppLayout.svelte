<script>
    import { page } from '@inertiajs/svelte';
    import Sidebar from '../Components/Sidebar.svelte';
    import Topbar from '../Components/Topbar.svelte';
    import Toast from '../Components/Toast.svelte';
    import { addToast } from '../stores/toast.svelte.js';

    let { title = 'Dashboard', children } = $props();

    let lastFlashKey = $state(null);

    $effect(() => {
        const flash = $page.props.flash ?? {};
        const errorsBag = $page.props.errors ?? {};

        const entry = flash.success
            ? { key: `success:${flash.success}`, title: 'Thành công', message: flash.success, type: 'success' }
            : flash.success_profile
              ? { key: `success_profile:${flash.success_profile}`, title: 'Thành công', message: flash.success_profile, type: 'success' }
              : flash.success_password
                ? { key: `success_password:${flash.success_password}`, title: 'Thành công', message: flash.success_password, type: 'success' }
                : flash.error
                  ? { key: `error:${flash.error}`, title: 'Lỗi', message: flash.error, type: 'error' }
                  : Object.values(errorsBag)[0]
                    ? { key: `validation:${Object.values(errorsBag)[0]}`, title: 'Lỗi', message: Object.values(errorsBag)[0], type: 'error', duration: 8000 }
                    : null;

        if (entry && entry.key !== lastFlashKey) {
            lastFlashKey = entry.key;
            addToast(entry);
        }
    });
</script>

<div class="flex h-full flex-1 overflow-hidden">
    <Sidebar navGroups={$page.props.navGroups} userName={$page.props.auth?.user?.name ?? ''} />

    <div class="flex min-w-0 flex-1 flex-col">
        <Topbar
            {title}
            dbStatus={$page.props.dbTrackerStatus}
            userName={$page.props.auth?.user?.name ?? ''}
            userJobTitle={$page.props.auth?.user?.job_title ?? ''}
            locale={$page.props.locale}
        />

        <main class="flex-1 overflow-y-auto px-6 py-6 sm:px-8">
            {@render children()}
        </main>
    </div>
</div>

<Toast />
