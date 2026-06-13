@extends('layouts.app')

@section('title', 'Team Members')
@section('page-title', 'Team Members')

@section('topbar-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i data-lucide="user-plus" width="14" height="14"></i>
        Add Member
    </a>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">All Personnel</span>
        <span class="text-sm text-muted">{{ $users->total() }} members</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Employee ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--color-brand-pale);color:var(--color-brand);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span style="font-weight:500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="font-family:monospace;font-size:13px;">{{ $user->employee_id }}</td>
                    <td style="font-size:13px;">{{ $user->email }}</td>
                    <td style="font-size:13px;color:var(--color-text-muted);">{{ $user->phone ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $user->department ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $user->isAdmin() ? 'badge-in_progress' : 'badge-skipped' }}" style="font-size:11px;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-done">Active</span>
                        @else
                            <span class="badge badge-skipped">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Deactivate this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--color-danger);">Deactivate</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($users->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--color-border);">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
