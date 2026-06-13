@extends('layouts.app')

@section('title', 'Edit Activity')
@section('page-title', 'Edit Activity')

@section('content')

<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Edit: {{ $activity->title }}</span>
            <a href="{{ route('activities.index') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="arrow-left" width="14" height="14"></i>
                Back
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('activities.update', $activity) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="title">Activity Title <span style="color:var(--color-danger)">*</span></label>
                    <input type="text" id="title" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           value="{{ old('title', $activity->title) }}" required>
                    @error('title') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control">{{ old('description', $activity->description) }}</textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $activity->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control"
                               value="{{ old('sort_order', $activity->sort_order) }}" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $activity->is_recurring) ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--color-brand);">
                        <span style="font-weight:600;font-size:13.5px;">Daily recurring activity</span>
                    </label>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $activity->is_active) ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--color-brand);">
                        <div>
                            <span style="font-weight:600;font-size:13.5px;">Active</span>
                            <div style="font-size:12px;color:var(--color-text-muted);">Uncheck to hide from daily dashboard without archiving</div>
                        </div>
                    </label>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--color-border);margin-top:8px;">
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
