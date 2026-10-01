@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $category->name ?? '') }}" required placeholder="Nombre de la categoría">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="description" class="form-label">Descripción</label>
        <input type="text" name="description" id="description"
               class="form-control @error('description') is-invalid @enderror"
               value="{{ old('description', $category->description ?? '') }}" placeholder="Descripción breve">
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Guardar
    </button>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
