@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="page-header no-print">
    <div>
        <h1 class="h4 mb-1">Reportes</h1>
        <p class="text-secondary mb-0">Resumen del inventario, stock y movimientos.</p>
    </div>
    <button type="button" class="btn btn-outline-dark" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Imprimir reporte
    </button>
</div>

{{-- INDICADORES --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">Productos</div>
                <div class="stat-value">{{ $summary['products'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">Unidades en stock</div>
                <div class="stat-value">{{ $summary['stock_total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">Valor del inventario</div>
                <div class="stat-value">${{ number_format($summary['value_total'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">Productos con stock bajo</div>
                <div class="stat-value text-danger">{{ $lowStock->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- STOCK POR CATEGORÍA --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>Stock por categoría</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Categoría</th>
                            <th class="text-center">Productos</th>
                            <th class="text-center">Unidades</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($byCategory as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="text-center cell-num">{{ $category->products_count }}</td>
                            <td class="text-center cell-num">{{ $category->stock_by_category ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MOVIMIENTOS --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>Resumen de movimientos</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th class="text-center">Operaciones</th>
                            <th class="text-center">Unidades</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge text-bg-success">Entradas</span></td>
                            <td class="text-center cell-num">{{ $summary['entries_count'] }}</td>
                            <td class="text-center cell-num">{{ $summary['entries_units'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="badge text-bg-warning text-dark">Salidas</span></td>
                            <td class="text-center cell-num">{{ $summary['exits_count'] }}</td>
                            <td class="text-center cell-num">{{ $summary['exits_units'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- INVENTARIO COMPLETO --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white d-flex justify-content-between">
        <strong>Inventario general (RF-11)</strong>
        <span class="text-secondary small">Total: {{ $products->count() }} productos</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Mínimo</th>
                    <th class="text-end">Valor</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td class="cell-truncate" title="{{ $product->name }}">
                        {{ $product->name }}
                        @if ($product->hasLowStock())
                            <span class="badge text-bg-danger">Stock bajo</span>
                        @endif
                    </td>
                    <td class="cell-truncate" title="{{ $product->category?->name }}">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-end cell-num">${{ number_format($product->price, 2) }}</td>
                    <td class="text-center cell-num">{{ $product->stock }}</td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                    <td class="text-end cell-num">${{ number_format($product->price * $product->stock, 2) }}</td>
                    <td class="text-center">
                        @if ($product->status === 'activo')
                            <span class="badge text-bg-success">Activo</span>
                        @else
                            <span class="badge text-bg-secondary">Inactivo</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">No hay productos registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- STOCK BAJO --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white"><strong class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Productos con stock bajo (RF-09)</strong></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Stock actual</th>
                    <th class="text-center">Stock mínimo</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($lowStock as $product)
                <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td class="cell-truncate" title="{{ $product->name }}">{{ $product->name }}</td>
                    <td class="cell-truncate" title="{{ $product->category?->name }}">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-center cell-num"><span class="badge text-bg-danger">{{ $product->stock }}</span></td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">Ningún producto tiene stock bajo.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
