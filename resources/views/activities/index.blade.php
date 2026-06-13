@extends('layouts.app')

@section('title', 'Manage Activities')
@section('page-title', 'Manage Activities')

@section('topbar-actions')
    <a href="{{ route('activities.create') }}" class="btn btn-primary btn-sm">
        <i data-lucide="plus" width="14" height="14"></i>
        New Activity
    </a>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">All Activities</span>
        <span class="text-sm text-muted">{{ $activities->total() }} total</span>
    </div>

    @if($activities->isEmpty())
        <div style="padding:40px;text-align:center;">
            <p class="text-muted">No activities yet. Create the first one!</p>
            <a href="{{ route('activities.create') }}" class="btn btn-primary" style="margin-top:16px;">Create Activity</a>
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Recurring</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Logs</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                    <tr style="{{ $activity->trashed() ? 'opacity:.55;' : '' }}">
                        <td class="text-muted text-sm">{{ $activity->sort_order }}</td>
                        <td>
                            <div style="font-weight:500;">{{ $activity->title }}</div>
                            @if($activity->description)
                                <div style="font-size:12px;color:var(--color-text-muted);">{{ Str::limit($activity->description, 70) }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12.5px;">{{ $activity->category_label }}</span>
                        </td>
                        <td>
                            @if($activity->is_recurring)
                                <span style="color:var(--color-done);font-size:12.5px;font-weight:500;">✓ Daily</span>
                            @else
                                <span class="text-muted text-sm">One-time</span>
                            @endif
                        </td>
                        <td>
                            @if($activity->trashed())
                                <span class="badge badge-skipped">Archived</span>
                            @elseif($activity->is_active)
                                <span class="badge badge-done">Active</span>
                            @else
                                <span class="badge badge-pending">Inactive</span>
                            @endif
                        </td>
                        <td style="font-size:12.5px;">{{ $activity->creator->name ?? '—' }}</td>
                        <td style="font-size:12.5px;">{{ $activity->activity_logs_count }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                @if($activity->trashed())
                                    <form method="POST" action="{{ route('activities.restore', $activity->id) }}">
                                        @csrf
                                        <button class="btn btn-secondary btn-sm">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('activities.edit', $activity) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                                          onsubmit="return confirm('Archive this activity?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--color-danger);">Archive</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($activities->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--color-border);">
                {{ $activities->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
