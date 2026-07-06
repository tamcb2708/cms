<style>
    /* Tailwind Fallback CSS */
    .dbt-grid { display: flex; flex-direction: column; gap: 1.5rem; flex: 1; min-height: 0; }
    @media (min-width: 1024px) {
        .dbt-grid { flex-direction: row; }
        .dbt-col-1 { width: 320px; flex-shrink: 0; display: flex; flex-direction: column; }
        .dbt-col-2 { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    }
    .dbt-collapsed { display: none !important; }
    .dbt-scroll-y { overflow-y: auto; overflow-x: hidden; }
    .dbt-scroll-auto { overflow: auto; }
    .dbt-table-wrapper { overflow: auto; flex: 1; min-height: 0; }
    .dbt-table { width: 100%; min-width: 700px; border-collapse: collapse; text-align: left; }
    
    .dbt-card { 
        background-color: var(--card-bg, #ffffff);
        color: var(--text-color, #111827);
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color, #f3f4f6);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        max-height: 100%;
    }
    .dbt-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color, #f3f4f6);
        background-color: var(--header-bg, #f9fafb);
    }
    .dbt-item {
        background-color: var(--item-bg, #f9fafb);
        border: 1px solid var(--border-color, #f3f4f6);
    }
    .dbt-table-th {
        background-color: var(--header-bg, #f9fafb);
        border-bottom: 1px solid var(--border-color, #f3f4f6);
    }
    .dbt-row {
        border-bottom: 1px solid var(--border-color, #f3f4f6);
    }
    .dbt-row:hover {
        background-color: var(--hover-bg, #f3f4f6);
    }
    
    /* Dark mode overrides */
    .dark .dbt-card {
        --card-bg: #1f2937;
        --text-color: #f9fafb;
        --border-color: rgba(255,255,255,0.05);
    }
    .dark .dbt-header, .dark .dbt-table-th {
        --header-bg: #111827; /* Solid color to prevent scrolling text overlap */
    }
    .dark .dbt-item {
        --item-bg: rgba(17,24,39,0.5);
    }
    .dark .dbt-row:hover {
        --hover-bg: rgba(255,255,255,0.05);
    }
    
    /* Fix missing tailwind classes */
    .dbt-badge-green { background-color: rgba(16, 185, 129, 0.1); color: #34d399; }
    .dbt-badge-red { background-color: rgba(239, 68, 68, 0.1); color: #f87171; }
    .dbt-badge-blue { background-color: rgba(59, 130, 246, 0.1); color: #60a5fa; }
    .dbt-badge-gray { background-color: rgba(107, 114, 128, 0.1); color: #9ca3af; }
    .dbt-text-red { color: #ef4444; cursor: pointer; transition: opacity 0.2s; }
    .dbt-text-red:hover { color: #b91c1c; opacity: 0.8; }
    .dbt-text-green { color: #10b981; cursor: pointer; transition: opacity 0.2s; }
    .dbt-text-green:hover { color: #047857; opacity: 0.8; }
    .dbt-text-accent { color: #8b5cf6; cursor: pointer; transition: opacity 0.2s; }
    .dbt-text-accent:hover { color: #7c3aed; opacity: 0.8; }
    button { cursor: pointer; }
    button:disabled { cursor: not-allowed; opacity: 0.5; }

    /* ── Data column: theme-neutral tags & diff ─────────────────────── */

    /* Shared tag base */
    .dbt-tag-insert,
    .dbt-tag-delete {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        margin: 2px;
        border: 1px solid;
    }

    /* INSERT tag — green tones, works on light & dark */
    .dbt-tag-insert {
        background-color: #f0fdf4;
        border-color: #bbf7d0;
    }
    .dbt-key { color: #15803d; font-weight: 600; }
    .dbt-tag-value { color: #166534; }

    /* DELETE tag — red tones */
    .dbt-tag-delete {
        background-color: #fff1f2;
        border-color: #fecdd3;
    }
    .dbt-key-red { color: #b91c1c; font-weight: 600; }
    .dbt-tag-value-red { color: #991b1b; }

    /* UPDATE diff row */
    .dbt-change-row { font-size: 11px; margin-bottom: 3px; line-height: 1.5; }
    .dbt-old-val { color: #dc2626; text-decoration: line-through; }
    .dbt-new-val { color: #16a34a; font-weight: 500; }
    .dbt-muted { color: #6b7280; }

    /* Dark mode overrides for data column */
    .dark .dbt-tag-insert { background-color: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.2); }
    .dark .dbt-key { color: #34d399; }
    .dark .dbt-tag-value { color: #a7f3d0; }

    .dark .dbt-tag-delete { background-color: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.2); }
    .dark .dbt-key-red { color: #f87171; }
    .dark .dbt-tag-value-red { color: #fecaca; }

    .dark .dbt-old-val { color: #f87171; }
    .dark .dbt-new-val { color: #4ade80; }
    .dark .dbt-muted { color: #9ca3af; }

</style>
