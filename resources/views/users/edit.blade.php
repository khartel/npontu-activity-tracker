@extends('layouts.app')

@section('title', 'Edit Member')
@section('page-title', 'Edit Team Member')

@section('content')

<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $user->name }}</span>
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="arrow-left" width="14" height="14"></i>
                Back
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name <span style="color:var(--color-danger)">*</span></label>
                        <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="employee_id">Employee ID <span style="color:var(--color-danger)">*</span></label>
                        <input type="text" id="employee_id" name="employee_id" class="form-control {{ $errors->has('employee_id') ? 'is-invalid' : '' }}"
                               value="{{ old('employee_id', $user->employee_id) }}" required>
                        @error('employee_id') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span style="color:var(--color-danger)">*</span></label>
                    <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="department">Department</label>
                        <input type="text" id="department" name="department" class="form-control"
                               value="{{ old('department', $user->department) }}">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="role">Role</label>
                        <select id="role" name="role" class="form-control" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="personnel" {{ old('role', $user->role) === 'personnel' ? 'selected' : '' }}>Personnel</option>
                            <option value="admin"     {{ old('role', $user->role) === 'admin'     ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div style="font-size:11.5px;color:var(--color-text-muted);margin-top:4px;">You cannot change your own role.</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label">Account Status</label>
                        <label style="display:flex;align-items:center;gap:8px;margin-top:10px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-brand);">
                            <span style="font-size:13.5px;font-weight:500;">Account is active</span>
                        </label>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="1">
                        @endif
                    </div>
                </div>

                <div style="background:var(--color-surface);border-radius:var(--radius);padding:14px;margin-bottom:16px;">
                    <div style="font-size:12.5px;font-weight:600;margin-bottom:10px;color:var(--color-text-muted);">Change Password (leave blank to keep current)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    @error('password') <div class="form-error" style="margin-top:6px;">{{ $message }}</div> @enderror
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--color-border);margin-top:8px;">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
