@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $product->code ?? '') }}" required placeholder="PRD-001">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $product->name ?? '') }}" required placeholder="Nombre del producto">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Descripción</label>
        <textarea name="description" id="description" rows="2"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Descripción breve del producto">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">Seleccione una categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="price" class="form-label">Precio ($) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="price" id="price"
               class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $product->price ?? '') }}" required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="stock" class="form-label">Stock actual <span class="text-danger">*</span></label>
        <input type="number" min="0" name="stock" id="stock"
               class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $product->stock ?? 0) }}" required>
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="stock_min" class="form-label">Stock mínimo <span class="text-danger">*</span></label>
        <input type="number" min="0" name="stock_min" id="stock_min"
               class="form-control @error('stock_min') is-invalid @enderror"
               value="{{ old('stock_min', $product->stock_min ?? 0) }}" required>
        @error('stock_min')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="activo" @selected(old('status', $product->status ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('status', $product->status ?? '') === 'inactivo')>Inactivo</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Guardar
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
