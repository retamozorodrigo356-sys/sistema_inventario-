@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Productos</h1>
        <p class="text-secondary mb-0">Consulta y administra los productos del inventario (RF-03, RF-11).</p>
    </div>
    @can('products.create')
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Registrar producto
        </a>
    @endcan
</div>

{{-- BUSCADOR Y FILTROS --}}
<form action="{{ route('products.index') }}" method="GET" class="filter-bar mb-3">
    <div class="row g-2">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Buscar por código o nombre..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="category_id" class="form-select">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="status" class="form-select">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(request('status') === 'activo')>Activo</option>
                <option value="inactivo" @selected(request('status') === 'inactivo')>Inactivo</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary flex-fill">
                <i class="bi bi-search me-1"></i>Buscar
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

{{-- TABLA DE PRODUCTOS --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Mínimo</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td>
                        {{ $product->name }}
                        @if ($product->hasLowStock())
                            <span class="badge text-bg-danger ms-1">Stock bajo</span>
                        @endif
                    </td>
                    <td class="cell-truncate" title="{{ $product->category?->name }}">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-end cell-num">${{ number_format($product->price, 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $product->hasLowStock() ? 'text-bg-danger' : 'text-bg-success' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                    <td class="text-center">
                        @if ($product->status === 'activo')
                            <span class="badge text-bg-success">Activo</span>
                        @else
                            <span class="badge text-bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center cell-actions">
                        @can('products.update')
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('products.delete')
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                    data-delete-url="{{ route('products.destroy', $product) }}"
                                    data-delete-name="{{ $product->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan
                        @cannot('products.update')
                            @cannot('products.delete')
                                <span class="text-secondary small">Solo lectura</span>
                            @endcannot
                        @endcannot
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        No se encontraron productos.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="card-footer bg-white">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
