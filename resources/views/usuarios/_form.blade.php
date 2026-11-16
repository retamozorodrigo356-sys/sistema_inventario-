@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">{{ __('app.users.full_name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="username" class="form-label">{{ __('app.common.username') }} <span class="text-danger">*</span></label>
        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror"
               value="{{ old('username', $user->username ?? '') }}" required>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="email" class="form-label">{{ __('app.common.email') }} <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Solo quien tiene permiso users.update puede asignar o cambiar roles. --}}
    @if (!isset($user) || auth()->user()->hasPermission('users.update'))
        <div class="col-12 col-md-6">
            <label for="role_id" class="form-label">{{ __('app.common.role') }} <span class="text-danger">*</span></label>
            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">{{ __('app.common.select_role') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id ?? '') == $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @else
        <div class="col-12 col-md-6">
            <span class="form-label d-block text-secondary">{{ __('app.common.role') }}</span>
            <span class="badge text-bg-secondary">{{ $user->role?->name ?? __('app.common.no_role') }}</span>
            <div class="form-text">{{ __('app.users.role_help') }}</div>
        </div>
    @endif

    <div class="col-12 col-md-6">
        <label for="password" class="form-label">
            {{ __('app.common.password') }} {!! isset($user) && $user->exists ? '<span class="text-secondary small">'.e(__('app.users.password_keep')).'</span>' : '<span class="text-danger">*</span>' !!}
        </label>
        <input type="password" name="password" id="password"
               class="form-control @error('password') is-invalid @enderror"
               {{ isset($user) && $user->exists ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="password_confirmation" class="form-label">{{ __('app.common.confirm_password') }}</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
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
    <a href="{{ auth()->user()->hasPermission('users.view') ? route('usuarios.index') : route('dashboard') }}" class="btn btn-outline-secondary">{{ __('app.common.cancel') }}</a>
</div>
