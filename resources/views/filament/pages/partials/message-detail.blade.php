<div style="display:flex; flex-direction:column; gap:12px; padding:8px 0;">
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f3f4f6;">
        <span style="font-size:13px; color:#6b7280;">수신자</span>
        <span style="font-size:13px; font-weight:600; color:#111827;">{{ $record->recipient }} ({{ $record->room }})</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f3f4f6;">
        <span style="font-size:13px; color:#6b7280;">발송 일시</span>
        <span style="font-size:13px; font-weight:600; color:#111827;">{{ $record->sent_at?->format('Y.m.d H:i:s') ?? '-' }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f3f4f6;">
        <span style="font-size:13px; color:#6b7280;">유형</span>
        <span style="font-size:13px; font-weight:600; color:#111827;">{{ $record->type }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f3f4f6;">
        <span style="font-size:13px; color:#6b7280;">상태</span>
        <span style="font-size:13px; font-weight:600; color:{{ $record->status === '발송 완료' ? '#059669' : '#ef4444' }};">{{ $record->status }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f3f4f6;">
        <span style="font-size:13px; color:#6b7280;">비용</span>
        <span style="font-size:13px; font-weight:600; color:#111827;">{{ $record->cost }}</span>
    </div>
    <div style="padding:8px 0;">
        <span style="display:block; font-size:13px; color:#6b7280; margin-bottom:8px;">내용</span>
        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px; font-size:13px; color:#374151; line-height:1.6; white-space:pre-wrap;">{{ $record->content }}</div>
    </div>
</div>
