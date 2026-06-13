@extends('layouts.app')

@section('title', 'New Activity')
@section('page-title', 'New Activity')

@section('content')

<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Activity Details</span>
            <a href="{{ route('activities.index') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="arrow-left" width="14" height="14"></i>
                Back
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('activities.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="title">Activity Title <span style="color:var(--color-danger)">*</span></label>
                    <input type="text" id="title" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           value="{{ old('title') }}" placeholder="e.g. Daily SMS Count vs Log Count" required>
                    @error('title') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Describe what this activity involves and how to complete it...">{{ old('description') }}</textarea>
                    @error('description') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="category">Category <span style="color:var(--color-danger)">*</span></label>
                        <select id="category" name="category" class="form-control {{ $errors->has('category') ? 'is-invalid' : '' }}" required>
                            <option value="">Select category…</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control"
                               value="{{ old('sort_order', 0) }}" min="0">
                        <div style="font-size:11.5px;color:var(--color-text-muted);margin-top:4px;">Lower numbers appear first</div>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', '1') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--color-brand);">
                        <div>
                            <div style="font-weight:600;font-size:13.5px;">Daily recurring activity</div>
                            <div style="font-size:12px;color:var(--color-text-muted);">This activity will appear on the dashboard every day</div>
                        </div>
                    </label>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--color-border);margin-top:8px;">
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="plus" width="14" height="14"></i>
                        Create Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
