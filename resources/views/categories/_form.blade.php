@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">{{ __('app.common.name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $category->name ?? '') }}" required placeholder="{{ __('app.categories.name_placeholder') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="description" class="form-label">{{ __('app.common.description') }}</label>
        <input type="text" name="description" id="description"
               class="form-control @error('description') is-invalid @enderror"
               value="{{ old('description', $category->description ?? '') }}" placeholder="{{ __('app.categories.description_placeholder') }}">
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>{{ __('app.common.save') }}
    </button>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">{{ __('app.common.cancel') }}</a>
</div>
