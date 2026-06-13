@extends('layouts.app')

@section('title', 'Add Team Member')
@section('page-title', 'Add Team Member')

@section('content')

<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">New Personnel Account</span>
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="arrow-left" width="14" height="14"></i>
                Back
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name <span style="color:var(--color-danger)">*</span></label>
                        <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name') }}" placeholder="Kwame Mensah" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="employee_id">Employee ID <span style="color:var(--color-danger)">*</span></label>
                        <input type="text" id="employee_id" name="employee_id" class="form-control {{ $errors->has('employee_id') ? 'is-invalid' : '' }}"
                               value="{{ old('employee_id') }}" placeholder="NPT-004" required>
                        @error('employee_id') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span style="color:var(--color-danger)">*</span></label>
                    <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" placeholder="name@npontu.com" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               value="{{ old('phone') }}" placeholder="+233 55 000 0000">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="department">Department</label>
                        <input type="text" id="department" name="department" class="form-control"
                               value="{{ old('department', 'Application Support') }}" placeholder="Application Support">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role <span style="color:var(--color-danger)">*</span></label>
                    <select id="role" name="role" class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                        <option value="personnel" {{ old('role') === 'personnel' ? 'selected' : '' }}>Personnel — Can update activity statuses</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator — Full access including activity management</option>
                    </select>
                    @error('role') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="password">Password <span style="color:var(--color-danger)">*</span></label>
                        <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password <span style="color:var(--color-danger)">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--color-border);margin-top:8px;">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="user-plus" width="14" height="14"></i>
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
