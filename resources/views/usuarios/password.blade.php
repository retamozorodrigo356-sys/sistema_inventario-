<x-layouts.app :title="__('app.users.password_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.users.password_heading') }}</h1>
        <p class="text-secondary mb-0">{!! __('app.users.password_subtitle', ['name' => e($user->name)]) !!}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.password.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="password" class="form-label">{{ __('app.common.new_password') }} <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="password_confirmation" class="form-label">{{ __('app.common.confirm_password') }} <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror" required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-light border small mt-3 mb-0">
                <i class="bi bi-shield-check me-1"></i>
                {!! __('app.users.password_hash_note') !!}
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>{{ __('app.common.save') }}
                </button>
                <a href="{{ route('usuarios.show', $user) }}" class="btn btn-outline-secondary">{{ __('app.common.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
