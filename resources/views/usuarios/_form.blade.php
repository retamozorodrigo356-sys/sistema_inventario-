@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="username" class="form-label">Usuario <span class="text-danger">*</span></label>
        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror"
               value="{{ old('username', $user->username ?? '') }}" required>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="role_id" class="form-label">Rol <span class="text-danger">*</span></label>
        <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
            <option value="">Seleccione un rol</option>
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

    <div class="col-12 col-md-6">
        <label for="password" class="form-label">
            Contraseña {!! isset($user) && $user->exists ? '<span class="text-secondary small">(dejar en blanco para no cambiar)</span>' : '<span class="text-danger">*</span>' !!}
        </label>
        <input type="password" name="password" id="password"
               class="form-control @error('password') is-invalid @enderror"
               {{ isset($user) && $user->exists ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
    </div>
</div>

<div class="alert alert-light border small mt-3 mb-0">
    <i class="bi bi-shield-check me-1"></i>
    Las contraseñas se almacenan cifradas con <strong>hash</strong> y nunca en texto plano (RNF-03).
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Guardar
    </button>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
