@if($status === 'not_connected')
    <tr><td colspan='4' class='text-center text-gray-500 py-8'>Chưa kết nối.</td></tr>
@elseif($status === 'not_initialized')
    <tr><td colspan='4' class='text-center text-gray-500 py-8'>Hệ thống chưa khởi tạo.</td></tr>
@elseif(empty($logs))
    <tr><td colspan='4' class='text-center text-gray-500 py-8'>Chưa có dữ liệu thay đổi nào.</td></tr>
@else
    @foreach($logs as $log)
        @php
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $time = date('H:i:s d/m/Y', strtotime($log['created_at'] . ' UTC'));
            $table = htmlspecialchars($log['table_name']);
            $action = $log['action'];
            
            $badgeColor = match($action) {
                'INSERT' => 'dbt-badge-green',
                'UPDATE' => 'dbt-badge-blue',
                'DELETE' => 'dbt-badge-red',
                default => 'dbt-badge-gray'
            };
            
            $old = $log['old_data'] ? json_decode($log['old_data'], true) : [];
            $new = $log['new_data'] ? json_decode($log['new_data'], true) : [];
            
            $changesHtml = '';
            
            if ($action === 'UPDATE') {
                $changes = [];
                foreach ($new as $k => $v) {
                    if (array_key_exists($k, $old) && $old[$k] !== $v) {
                        $oVal = is_array($old[$k]) ? json_encode($old[$k]) : (string)$old[$k];
                        $nVal = is_array($v) ? json_encode($v) : (string)$v;
                        if (strlen($oVal) > 60) $oVal = substr($oVal, 0, 60) . '...';
                        if (strlen($nVal) > 60) $nVal = substr($nVal, 0, 60) . '...';
                        $changes[] = "<div class='dbt-change-row'><b class='dbt-key'>".htmlspecialchars($k).":</b> <s class='dbt-old-val'>".htmlspecialchars($oVal)."</s> <span class='dbt-new-val'>➔ ".htmlspecialchars($nVal)."</span></div>";
                    }
                }
                $changesHtml = empty($changes) ? "<span class='dbt-muted text-xs'>Cập nhật ngầm</span>" : implode("", $changes);
            } elseif ($action === 'INSERT') {
                foreach ($new as $k => $v) {
                    if ($v === null || $v === '') continue;
                    $val = is_array($v) ? json_encode($v) : (string)$v;
                    if (strlen($val) > 40) $val = substr($val, 0, 40) . '...';
                    $changesHtml .= "<span class='dbt-tag-insert'><span class='dbt-key'>".htmlspecialchars($k).":</span> <span class='dbt-tag-value'>".htmlspecialchars($val)."</span></span> ";
                }
            } elseif ($action === 'DELETE') {
                foreach ($old as $k => $v) {
                    if ($v === null || $v === '') continue;
                    $val = is_array($v) ? json_encode($v) : (string)$v;
                    if (strlen($val) > 40) $val = substr($val, 0, 40) . '...';
                    $changesHtml .= "<span class='dbt-tag-delete'><span class='dbt-key-red'>".htmlspecialchars($k).":</span> <span class='dbt-tag-value-red'>".htmlspecialchars($val)."</span></span> ";
                }
            }
        @endphp
        
        <tr class='dbt-row transition-colors'>
            <td class='p-3 text-xs whitespace-nowrap' style='color: var(--text-color, #6b7280)'>{{ $time }}</td>
            <td class='p-3 text-sm font-medium'>{{ $table }}</td>
            <td class='p-3'>
                <span class='px-2 py-0.5 rounded text-[10px] font-bold border uppercase {{ $badgeColor }}' style='border-color: currentColor;'>{{ $action }}</span>
            </td>
            <td class='p-3'>{!! $changesHtml !!}</td>
        </tr>
    @endforeach
@endif
