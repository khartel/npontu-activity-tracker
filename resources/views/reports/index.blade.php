@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Activity Reports')

@section('topbar-actions')
    <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-secondary btn-sm">
        <i data-lucide="download" width="14" height="14"></i>
        Export CSV
    </a>
@endsection

@push('styles')
<style>
    .filter-card {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        margin-bottom: 22px;
    }
    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: flex-end;
    }
    .stats-row {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 22px;
    }
    .stat-tile {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 14px 16px;
        text-align: center;
    }
    .stat-value {
        font-family: var(--font-display);
        font-size: 24px;
        font-weight: 700;
        color: var(--color-text);
    }
    .stat-label { font-size: 11.5px; color: var(--color-text-muted); margin-top: 2px; }

    .day-section { margin-bottom: 20px; }
    .day-heading {
        font-family: var(--font-display);
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text-muted);
        padding: 8px 0;
        border-bottom: 1px solid var(--color-border);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .history-toggle {
        font-size: 11.5px;
        color: var(--color-brand);
        cursor: pointer;
        background: none;
        border: none;
        font-weight: 500;
    }
    .history-row {
        display: none;
        padding: 10px 16px;
        background: var(--color-surface);
        border-radius: var(--radius);
        margin-top: 4px;
        font-size: 12.5px;
    }
    .history-entry {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        padding: 5px 0;
        border-bottom: 1px solid var(--color-border);
    }
    .history-entry:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

{{-- ── Filters ── --}}
<form method="GET" action="{{ route('reports.index') }}" class="filter-card">
    <div style="margin-bottom:12px;">
        <h3 style="font-family:var(--font-display);font-size:14px;font-weight:600;">Filter Report</h3>
    </div>
    <div class="filter-row">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">From Date</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">To Date</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}" max="{{ now()->toDateString() }}">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Activity</label>
            <select name="activity_id" class="form-control">
                <option value="">All Activities</option>
                @foreach($activities as $act)
                    <option value="{{ $act->id }}" {{ $activityId == $act->id ? 'selected' : '' }}>
                        {{ $act->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                @foreach($statuses as $key => $cfg)
                    <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="height:38px;">
                <i data-lucide="search" width="14" height="14"></i>
                Apply
            </button>
        </div>
    </div>
</form>

{{-- ── Stats ── --}}
<div class="stats-row">
    <div class="stat-tile">
        <div class="stat-value">{{ $stats['days_covered'] }}</div>
        <div class="stat-label">Days Covered</div>
    </div>
    <div class="stat-tile">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Entries</div>
    </div>
    <div class="stat-tile">
        <div class="stat-value" style="color:var(--color-done)">{{ $stats['done'] }}</div>
        <div class="stat-label">Done</div>
    </div>
    <div class="stat-tile">
        <div class="stat-value" style="color:var(--color-pending)">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-tile">
        <div class="stat-value" style="color:var(--color-inprogress)">{{ $stats['in_progress'] }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-tile">
        <div class="stat-value">{{ $stats['total'] > 0 ? round($stats['done'] / $stats['total'] * 100) : 0 }}%</div>
        <div class="stat-label">Completion Rate</div>
    </div>
</div>

{{-- ── Results ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Activity Log — {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
        <span class="text-sm text-muted">{{ $logs->total() }} entries</span>
    </div>

    @if($logs->isEmpty())
        <div style="padding:40px;text-align:center;">
            <p class="text-muted">No records found for the selected filters.</p>
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Updated By</th>
                    <th>Time</th>
                    <th>SMS Data</th>
                    <th>Remark</th>
                    <th>History</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;">{{ $log->log_date->format('d M Y') }}</td>
                        <td>
                            <span style="font-weight:500;">{{ $log->activity->title ?? '—' }}</span>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--color-text-muted);">{{ $log->activity->category_label ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $log->status }}">{{ $log->status_label }}</span>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:500;">{{ $log->updated_by_name ?? $log->user->name ?? '—' }}</div>
                            <div style="font-size:11.5px;color:var(--color-text-muted);">{{ $log->updated_by_employee_id ?? '' }}</div>
                        </td>
                        <td style="white-space:nowrap;font-size:12.5px;color:var(--color-text-muted);">
                            {{ $log->status_updated_at?->format('H:i') ?? '—' }}
                        </td>
                        <td>
                            @if($log->hasSmsData())
                                <div style="font-size:12px;line-height:1.5;">
                                    <div>Sys: <strong>{{ number_format($log->sms_system_count) }}</strong></div>
                                    <div>Log: <strong>{{ number_format($log->sms_log_count) }}</strong></div>
                                    @if(abs($log->sms_discrepancy) > 5)
                                        <div style="color:var(--color-pending);font-weight:600;">Diff: {{ $log->sms_discrepancy > 0 ? '+' : '' }}{{ $log->sms_discrepancy }} ⚠</div>
                                    @else
                                        <div style="color:var(--color-done);">Diff: {{ $log->sms_discrepancy }} ✓</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted text-xs">—</span>
                            @endif
                        </td>
                        <td style="max-width:180px;font-size:12.5px;color:var(--color-text-muted);">
                            {{ $log->remark ? Str::limit($log->remark, 60) : '—' }}
                        </td>
                        <td>
                            @if($log->history->isNotEmpty())
                                <button class="btn btn-ghost btn-sm" onclick="toggleHistory({{ $log->id }})">
                                    {{ $log->history->count() }} update{{ $log->history->count() > 1 ? 's' : '' }}
                                </button>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                    </tr>

                    {{-- History expansion row --}}
                    @if($log->history->isNotEmpty())
                        <tr id="history-{{ $log->id }}" style="display:none;">
                            <td colspan="9" style="padding:0 16px 12px;">
                                <div style="background:var(--color-surface);border-radius:var(--radius);padding:12px 14px;">
                                    <div style="font-size:12px;font-weight:600;color:var(--color-text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Full Update History</div>
                                    @foreach($log->history as $h)
                                        <div class="history-entry">
                                            <div style="flex:1;">
                                                <span class="badge badge-{{ $h->status_after }}" style="font-size:10px;padding:2px 7px;">{{ \App\Models\ActivityLog::$statuses[$h->status_after]['label'] }}</span>
                                                <span style="margin-left:6px;font-weight:500;">{{ $h->personnel_name }}</span>
                                                <span style="color:var(--color-text-muted);">({{ $h->personnel_employee_id }}, {{ $h->personnel_email }})</span>
                                                @if($h->remark)
                                                    <div style="margin-top:3px;font-style:italic;color:var(--color-text-muted);">"{{ $h->remark }}"</div>
                                                @endif
                                            </div>
                                            <div style="font-size:11.5px;color:var(--color-text-muted);white-space:nowrap;">
                                                {{ $h->updated_at_time->format('d M H:i') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--color-border);display:flex;justify-content:flex-end;">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleHistory(logId) {
    const row = document.getElementById('history-' + logId);
    if (row) {
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    }
}
</script>
@endpush
