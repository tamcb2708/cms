<script>
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import StatsRow from '../../Components/Dashboard/StatsRow.svelte';
    import GrowthChart from '../../Components/Dashboard/GrowthChart.svelte';
    import StatusChart from '../../Components/Dashboard/StatusChart.svelte';
    import RecentTenants from '../../Components/Dashboard/RecentTenants.svelte';
    import TeamAccess from '../../Components/Dashboard/TeamAccess.svelte';

    let { tenantStats, growth, recentTenants, team, tenantsUrl } = $props();
</script>

<AppLayout title="Dashboard">
    <div class="mx-auto flex max-w-6xl flex-col gap-6">
        <StatsRow {tenantStats} />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <GrowthChart {growth} />
            <StatusChart {tenantStats} />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <RecentTenants {recentTenants} {tenantsUrl} />
            <TeamAccess {team} />
        </div>
    </div>
</AppLayout>
