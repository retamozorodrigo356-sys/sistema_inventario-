@extends('layouts.app')

@section('title', 'Registrar entrada')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Registrar entrada</h1>
        <p class="text-secondary mb-0">Al registrar la entrada el stock del producto aumenta automáticamente (RF-05, RF-07).</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('entradas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Producto <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">Seleccione un producto</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                    {{ $product->code }} — {{ $product->name }} (stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" min="1" name="quantity" id="quantity"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="date" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date"
                                   class="form-control @error('date') is-invalid @enderror"
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="observation" class="form-label">Observación</label>
                        <textarea name="observation" id="observation" rows="2"
                                  class="form-control @error('observation') is-invalid @enderror"
                                  placeholder="Ej. Ingreso de mercadería">{{ old('observation') }}</textarea>
                        @error('observation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Registrar entrada
                        </button>
                        <a href="{{ route('entradas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
