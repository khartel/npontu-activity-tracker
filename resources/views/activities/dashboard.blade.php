@extends('layouts.app')

@section('title', 'Daily Dashboard')
@section('page-title', 'Daily Activity Dashboard')

@section('topbar-actions')
    {{-- Date Navigator --}}
    <form method="GET" action="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:8px;">
        <input type="date"
               name="date"
               value="{{ $date }}"
               max="{{ now()->toDateString() }}"
               class="form-control"
               style="width:160px;padding:6px 10px;"
               onchange="this.form.submit()">
        @if(!$isToday)
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Today</a>
        @endif
    </form>
@endsection

@push('styles')
<style>
    /* ── Summary Strip ───────────────────────────────────────────────── */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    .summary-tile {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .summary-tile .tile-value {
        font-family: var(--font-display);
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }
    .summary-tile .tile-label {
        font-size: 12px;
        color: var(--color-text-muted);
        font-weight: 500;
    }
    .tile-total   .tile-value { color: var(--color-text); }
    .tile-done    .tile-value { color: var(--color-done); }
    .tile-pending .tile-value { color: var(--color-pending); }
    .tile-inprog  .tile-value { color: var(--color-inprogress); }
    .tile-skipped .tile-value { color: var(--color-skipped); }

    /* Progress bar */
    .progress-bar-wrap {
        height: 6px;
        background: var(--color-border);
        border-radius: 4px;
        overflow: hidden;
        margin-top: 6px;
    }
    .progress-bar-fill {
        height: 100%;
        background: var(--color-done);
        border-radius: 4px;
        transition: width .4s ease;
    }

    /* ── Activity Grid ───────────────────────────────────────────────── */
    .activity-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 28px;
    }
    .activity-row {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: start;
        transition: box-shadow .15s;
    }
    .activity-row:hover { box-shadow: var(--shadow-sm); }
    .activity-row.status-done   { border-left: 3px solid var(--color-done); }
    .activity-row.status-pending { border-left: 3px solid var(--color-pending); }
    .activity-row.status-in_progress { border-left: 3px solid var(--color-inprogress); }
    .activity-row.status-skipped { border-left: 3px solid var(--color-skipped); }

    .act-info .act-title {
        font-weight: 600;
        font-size: 14px;
        color: var(--color-text);
        margin-bottom: 3px;
    }
    .act-info .act-desc {
        font-size: 12.5px;
        color: var(--color-text-muted);
        line-height: 1.5;
    }
    .act-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }
    .category-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: var(--color-surface);
        color: var(--color-text-muted);
        border: 1px solid var(--color-border);
    }
    .act-updater {
        font-size: 11.5px;
        color: var(--color-text-muted);
    }
    .sms-inline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        background: #f0f4ff;
        border: 1px solid #c7d7fd;
        border-radius: 6px;
        padding: 3px 8px;
        color: #3b4da8;
    }
    .sms-inline.discrepancy { background: #fff7ed; border-color: #fcd9a0; color: #b45309; }

    /* Action area */
    .act-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    /* ── Update Modal form ───────────────────────────────────────────── */
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 16px;
    }
    .status-radio {
        display: none;
    }
    .status-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 12px;
        border: 1.5px solid var(--color-border);
        border-radius: var(--radius);
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text-muted);
        transition: all .15s;
        text-align: center;
    }
    .status-radio:checked + .status-label {
        border-color: var(--color-brand);
        background: var(--color-brand-pale);
        color: var(--color-brand);
    }
    .status-radio[value="done"]:checked + .status-label    { border-color: var(--color-done); background: #dcfce7; color: var(--color-done); }
    .status-radio[value="pending"]:checked + .status-label { border-color: var(--color-pending); background: #fef3c7; color: var(--color-pending); }
    .status-radio[value="in_progress"]:checked + .status-label { border-color: var(--color-inprogress); background: #dbeafe; color: var(--color-inprogress); }
    .status-radio[value="skipped"]:checked + .status-label  { border-color: var(--color-skipped); background: #f3f4f6; color: var(--color-skipped); }

    /* ── Handover Panel ──────────────────────────────────────────────── */
    .handover-panel {
        margin-top: 8px;
    }
    .handover-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--color-border);
    }
    .handover-item:last-child { border-bottom: none; }
    .handover-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: var(--color-brand-pale);
        color: var(--color-brand);
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700;
        flex-shrink: 0;
    }
    .handover-content { flex: 1; }
    .handover-headline { font-size: 13px; font-weight: 600; color: var(--color-text); }
    .handover-meta { font-size: 12px; color: var(--color-text-muted); margin-top: 2px; }
    .handover-remark { font-size: 12.5px; color: var(--color-text); margin-top: 4px; font-style: italic; }
    .handover-time {
        font-size: 11.5px;
        color: var(--color-text-muted);
        white-space: nowrap;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')

{{-- ── Date Header ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <div>
        <h2 style="font-family:var(--font-display);font-size:20px;font-weight:700;">
            {{ $isToday ? 'Today — ' : '' }}{{ $viewDate->format('l, d F Y') }}
        </h2>
        @if(!$isToday)
            <p class="text-sm text-muted" style="margin-top:2px;">Viewing historical data — updates are read-only.</p>
        @endif
    </div>
</div>

{{-- ── Summary Strip ── --}}
<div class="summary-strip">
    <div class="summary-tile tile-total">
        <span class="tile-value">{{ $summary['total'] }}</span>
        <span class="tile-label">Total Activities</span>
    </div>
    <div class="summary-tile tile-done">
        <span class="tile-value">{{ $summary['done'] }}</span>
        <span class="tile-label">Done</span>
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" style="width:{{ $summary['total'] > 0 ? round($summary['done'] / $summary['total'] * 100) : 0 }}%"></div>
        </div>
    </div>
    <div class="summary-tile tile-pending">
        <span class="tile-value">{{ $summary['pending'] }}</span>
        <span class="tile-label">Pending</span>
    </div>
    <div class="summary-tile tile-inprog">
        <span class="tile-value">{{ $summary['in_progress'] }}</span>
        <span class="tile-label">In Progress</span>
    </div>
    <div class="summary-tile tile-skipped">
        <span class="tile-value">{{ $summary['skipped'] }}</span>
        <span class="tile-label">Skipped</span>
    </div>
</div>

{{-- ── Two-column layout: Activities | Handover ── --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- Activities Column --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <h3 style="font-family:var(--font-display);font-size:15px;font-weight:600;">Activities</h3>
            @if($isToday)
                <span class="text-xs text-muted">Click "Update" to log your status</span>
            @endif
        </div>

        <div class="activity-grid">
            @forelse($activities as $activity)
                @php
                    $log = $activity->activityLogs->first();
                    $status = $log?->status ?? 'pending';
                    $lastHistory = $log?->history?->first();
                @endphp
                <div class="activity-row status-{{ $status }}">
                    <div class="act-info">
                        <div class="act-title">{{ $activity->title }}</div>
                        @if($activity->description)
                            <div class="act-desc">{{ $activity->description }}</div>
                        @endif

                        <div class="act-meta">
                            <span class="category-pill">{{ $activity->category_label }}</span>

                            @if($log && $log->hasSmsData())
                                <span class="sms-inline {{ abs($log->sms_discrepancy ?? 0) > 5 ? 'discrepancy' : '' }}">
                                    Sys: {{ number_format($log->sms_system_count) }}
                                    | Log: {{ number_format($log->sms_log_count) }}
                                    | Diff: {{ $log->sms_discrepancy > 0 ? '+' : '' }}{{ $log->sms_discrepancy }}
                                </span>
                            @endif

                            @if($log?->updated_by_name)
                                <span class="act-updater">
                                    Updated by {{ $log->updated_by_name }} ({{ $log->updated_by_employee_id }})
                                    at {{ $log->status_updated_at?->format('H:i') }}
                                </span>
                            @endif
                        </div>

                        @if($log?->remark)
                            <div style="margin-top:6px;font-size:12.5px;color:var(--color-text-muted);font-style:italic;">
                                "{{ $log->remark }}"
                            </div>
                        @endif
                    </div>

                    <div class="act-actions">
                        <span class="badge badge-{{ $status }}">
                            {{ \App\Models\ActivityLog::$statuses[$status]['label'] ?? ucfirst($status) }}
                        </span>

                        @if($isToday)
                            <button
                                class="btn btn-secondary btn-sm"
                                onclick="openUpdateModal({{ $activity->id }}, '{{ addslashes($activity->title) }}', {{ $log?->id ?? 'null' }}, '{{ $status }}', '{{ addslashes($log?->remark ?? '') }}', {{ $log?->sms_system_count ?? 'null' }}, {{ $log?->sms_log_count ?? 'null' }}, '{{ $activity->category }}')">
                                Update
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card" style="text-align:center;padding:40px;">
                    <p class="text-muted">No activities configured. Ask an admin to add activities.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Handover Column --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="font-size:14px;">
                    <i data-lucide="git-pull-request" width="15" height="15" style="display:inline;vertical-align:middle;margin-right:6px;"></i>
                    Handover Log
                </span>
                <span class="text-xs text-muted">{{ $viewDate->format('d M') }}</span>
            </div>
            <div class="card-body" style="padding:12px 16px;">
                @if($dayHistory->isEmpty())
                    <p class="text-muted text-sm" style="text-align:center;padding:20px 0;">
                        No updates recorded yet today.
                    </p>
                @else
                    <div class="handover-panel">
                        @foreach($dayHistory as $entry)
                            <div class="handover-item">
                                <div class="handover-avatar">
                                    {{ strtoupper(substr($entry->personnel_name, 0, 2)) }}
                                </div>
                                <div class="handover-content">
                                    <div class="handover-headline">{{ $entry->activity->title ?? 'Unknown Activity' }}</div>
                                    <div class="handover-meta">
                                        <span class="badge badge-{{ $entry->status_after }}" style="font-size:10px;padding:2px 7px;">
                                            {{ \App\Models\ActivityLog::$statuses[$entry->status_after]['label'] }}
                                        </span>
                                        &nbsp;by <strong>{{ $entry->personnel_name }}</strong>
                                        ({{ $entry->personnel_employee_id }})
                                    </div>
                                    @if($entry->remark)
                                        <div class="handover-remark">"{{ Str::limit($entry->remark, 80) }}"</div>
                                    @endif
                                </div>
                                <div class="handover-time">
                                    {{ $entry->updated_at_time->format('H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Pending activities highlight --}}
        @php
            $pendingActivities = $activities->filter(fn($a) => ($a->activityLogs->first()?->status ?? 'pending') === 'pending');
        @endphp
        @if($pendingActivities->isNotEmpty())
            <div class="card" style="margin-top:16px;border-color:#fde68a;">
                <div class="card-header" style="border-color:#fde68a;background:#fffbeb;">
                    <span class="card-title" style="font-size:13px;color:#b45309;">
                        <i data-lucide="alert-triangle" width="14" height="14" style="display:inline;vertical-align:middle;margin-right:6px;color:#d97706"></i>
                        Pending Handover ({{ $pendingActivities->count() }})
                    </span>
                </div>
                <div class="card-body" style="padding:12px 16px;">
                    @foreach($pendingActivities as $pa)
                        <div style="padding:6px 0;border-bottom:1px solid var(--color-border);font-size:13px;">
                            {{ $pa->title }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ── Update Modal ── --}}
<div class="modal-overlay" id="updateModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modalTitle">Update Activity</span>
            <button class="modal-close" onclick="closeModal()">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="updateForm">
                @csrf
                <input type="hidden" id="modalDate" name="date" value="{{ $date }}">

                <div class="form-group">
                    <div class="form-label" style="margin-bottom:10px;">Status</div>
                    <div class="status-grid">
                        @foreach(\App\Models\ActivityLog::$statuses as $key => $cfg)
                            <div>
                                <input type="radio" class="status-radio" name="status" id="status_{{ $key }}" value="{{ $key }}">
                                <label class="status-label" for="status_{{ $key }}">{{ $cfg['label'] }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="smsFields" style="display:none;">
                    <div style="background:var(--color-surface);border-radius:var(--radius);padding:14px;margin-bottom:16px;">
                        <div class="form-label" style="margin-bottom:10px;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:var(--color-text-muted);">SMS Count Comparison</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="sms_system_count">System Count</label>
                                <input type="number" id="sms_system_count" name="sms_system_count" class="form-control" min="0" placeholder="0">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="sms_log_count">Log Count</label>
                                <input type="number" id="sms_log_count" name="sms_log_count" class="form-control" min="0" placeholder="0">
                            </div>
                        </div>
                        <div id="smsDiscrepancyDisplay" style="margin-top:10px;font-size:12.5px;font-weight:600;display:none;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="remark">Remark</label>
                    <textarea id="remark" name="remark" class="form-control" placeholder="Add any notes or observations..."></textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:4px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentActivityId = null;
let currentLogId      = null;

function openUpdateModal(activityId, title, logId, status, remark, smsSystem, smsLog, category) {
    currentActivityId = activityId;
    currentLogId      = logId;

    document.getElementById('modalTitle').textContent = 'Update: ' + title;

    // Pre-select status
    const radio = document.querySelector(`input[name="status"][value="${status}"]`);
    if (radio) radio.checked = true;

    // Pre-fill remark
    document.getElementById('remark').value = remark || '';

    // Show SMS fields only for SMS monitoring activities
    const smsDiv = document.getElementById('smsFields');
    if (category === 'sms_monitoring') {
        smsDiv.style.display = 'block';
        document.getElementById('sms_system_count').value = smsSystem || '';
        document.getElementById('sms_log_count').value    = smsLog    || '';
        updateSmsDiscrepancy();
    } else {
        smsDiv.style.display = 'none';
    }

    document.getElementById('updateModal').classList.add('open');
    lucide.createIcons();
}

function closeModal() {
    document.getElementById('updateModal').classList.remove('open');
    currentActivityId = null;
    currentLogId      = null;
}

// Close on overlay click
document.getElementById('updateModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Live SMS discrepancy display
['sms_system_count','sms_log_count'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateSmsDiscrepancy);
});

function updateSmsDiscrepancy() {
    const sys  = parseInt(document.getElementById('sms_system_count').value) || 0;
    const log  = parseInt(document.getElementById('sms_log_count').value)    || 0;
    const diff = sys - log;
    const el   = document.getElementById('smsDiscrepancyDisplay');
    if (sys || log) {
        el.style.display = 'block';
        el.style.color   = Math.abs(diff) > 5 ? '#b45309' : '#15803d';
        el.textContent   = `Discrepancy: ${diff > 0 ? '+' : ''}${diff}${Math.abs(diff) > 5 ? ' ⚠ Review required' : ' ✓'}`;
    } else {
        el.style.display = 'none';
    }
}

// AJAX form submission
document.getElementById('updateForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!currentActivityId) return;

    const btn = document.getElementById('saveBtn');
    btn.disabled    = true;
    btn.textContent = 'Saving...';

    const formData = new FormData(this);
    const data     = Object.fromEntries(formData.entries());

    try {
        const url = `/activities/${currentActivityId}/quick-update`;
        const res = await fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     window.csrfToken,
                'Accept':           'application/json',
            },
            body: JSON.stringify(data),
        });

        const json = await res.json();

        if (json.success) {
            closeModal();
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert('Error: ' + (json.message || 'Could not save update.'));
        }
    } catch (err) {
        alert('Network error. Please try again.');
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Save Update';
    }
});
</script>
@endpush
