@csrf

<div class="mb-3">
    <label for="product_id" class="form-label">{{ __('app.common.product') }} <span class="text-danger">*</span></label>
    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
        <option value="">{{ __('app.common.select_product') }}</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}" @selected(old('product_id', $movement->product_id ?? '') == $product->id)>
                {{ $product->code }} — {{ $product->name }} ({{ __('app.common.stock') }}: {{ $product->stock }})
            </option>
        @endforeach
    </select>
    @error('product_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="quantity" class="form-label">{{ __('app.common.quantity') }} <span class="text-danger">*</span></label>
        <input type="number" min="1" name="quantity" id="quantity"
               class="form-control @error('quantity') is-invalid @enderror"
               value="{{ old('quantity', $movement->quantity ?? '') }}" required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="date" class="form-label">{{ __('app.common.date') }} <span class="text-danger">*</span></label>
        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
               value="{{ old('date', isset($movement) ? $movement->date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-3">
    <label for="observation" class="form-label">{{ __('app.common.observation') }}</label>
    <textarea name="observation" id="observation" rows="2"
              class="form-control @error('observation') is-invalid @enderror"
              placeholder="{{ __('app.entries.observation_placeholder') }}">{{ old('observation', $movement->observation ?? '') }}</textarea>
    @error('observation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn {{ $submitClass ?? 'btn-success' }}">
        <i class="bi bi-check-lg me-1"></i>{{ $submitLabel ?? __('app.entries.submit') }}
    </button>
    <a href="{{ route('entradas.index') }}" class="btn btn-outline-secondary">{{ __('app.common.cancel') }}</a>
</div>
