<script>
    let { status = 'not_connected', logs = [] } = $props();

    const badgeClass = { INSERT: 'dbt-badge-green', UPDATE: 'dbt-badge-blue', DELETE: 'dbt-badge-red' };

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function truncate(value, max) {
        const str = typeof value === 'object' ? JSON.stringify(value) : String(value);
        return str.length > max ? `${str.slice(0, max)}...` : str;
    }

    function formatTime(createdAt) {
        const date = new Date(`${createdAt.replace(' ', 'T')}Z`);
        return new Intl.DateTimeFormat('vi-VN', {
            timeZone: 'Asia/Ho_Chi_Minh',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            day: '2-digit', month: '2-digit', year: 'numeric',
        }).format(date).replace(',', '');
    }

    function buildChangesHtml(log) {
        const old = log.old_data ? JSON.parse(log.old_data) : {};
        const fresh = log.new_data ? JSON.parse(log.new_data) : {};

        if (log.action === 'UPDATE') {
            const rows = Object.entries(fresh)
                .filter(([k, v]) => Object.prototype.hasOwnProperty.call(old, k) && old[k] !== v)
                .map(([k, v]) => `<div class="dbt-change-row"><b class="dbt-key">${escapeHtml(k)}:</b> <s class="dbt-old-val">${escapeHtml(truncate(old[k], 60))}</s> <span class="dbt-new-val">➔ ${escapeHtml(truncate(v, 60))}</span></div>`);
            return rows.length ? rows.join('') : '<span class="dbt-muted text-xs">Cập nhật ngầm</span>';
        }

        if (log.action === 'INSERT') {
            return Object.entries(fresh)
                .filter(([, v]) => v !== null && v !== '')
                .map(([k, v]) => `<span class="dbt-tag-insert"><span class="dbt-key">${escapeHtml(k)}:</span> <span class="dbt-tag-value">${escapeHtml(truncate(v, 40))}</span></span> `)
                .join('');
        }

        if (log.action === 'DELETE') {
            return Object.entries(old)
                .filter(([, v]) => v !== null && v !== '')
                .map(([k, v]) => `<span class="dbt-tag-delete"><span class="dbt-key-red">${escapeHtml(k)}:</span> <span class="dbt-tag-value-red">${escapeHtml(truncate(v, 40))}</span></span> `)
                .join('');
        }

        return '';
    }

    const rows = $derived(logs.map((log) => ({
        time: formatTime(log.created_at),
        table: log.table_name,
        action: log.action,
        badge: badgeClass[log.action] ?? 'dbt-badge-gray',
        changesHtml: buildChangesHtml(log),
    })));
</script>

{#if status === 'not_connected'}
    <tr><td colspan="4" class="py-8 text-center text-gray-500">Chưa kết nối.</td></tr>
{:else if status === 'not_initialized'}
    <tr><td colspan="4" class="py-8 text-center text-gray-500">Hệ thống chưa khởi tạo.</td></tr>
{:else if rows.length === 0}
    <tr><td colspan="4" class="py-8 text-center text-gray-500">Chưa có dữ liệu thay đổi nào.</td></tr>
{:else}
    {#each rows as row}
        <tr class="dbt-row transition-colors">
            <td class="p-3 text-xs whitespace-nowrap" style="color: var(--text-color, #6b7280)">{row.time}</td>
            <td class="p-3 text-sm font-medium">{row.table}</td>
            <td class="p-3">
                <span class="rounded border px-2 py-0.5 text-[10px] font-bold uppercase {row.badge}" style="border-color: currentColor;">{row.action}</span>
            </td>
            <td class="p-3">{@html row.changesHtml}</td>
        </tr>
    {/each}
{/if}
