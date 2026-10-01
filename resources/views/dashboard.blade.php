@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="h4 mb-1">Panel general</h1>
<p class="text-secondary mb-4">Resumen del inventario en tiempo real (RF-12).</p>

{{-- TARJETAS DEL DASHBOARD --}}
<div class="row g-3">
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                <div class="ms-3">
                    <div class="stat-label">Total de productos</div>
                    <div class="stat-value">{{ $totals['products'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-tags"></i></div>
                <div class="ms-3">
                    <div class="stat-label">Total de categorías</div>
                    <div class="stat-value">{{ $totals['categories'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="ms-3">
                    <div class="stat-label">Productos con stock bajo</div>
                    <div class="stat-value text-danger">{{ $totals['low_stock'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-box-arrow-in-down"></i></div>
                <div class="ms-3">
                    <div class="stat-label">Total de entradas</div>
                    <div class="stat-value">{{ $totals['entries'] }}</div>
                    <small class="text-secondary">{{ $entryUnits }} unidades</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-box-arrow-up"></i></div>
                <div class="ms-3">
                    <div class="stat-label">Total de salidas</div>
                    <div class="stat-value">{{ $totals['exits'] }}</div>
                    <small class="text-secondary">{{ $exitUnits }} unidades</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STOCK BAJO + MOVIMIENTOS RECIENTES --}}
<div class="row g-3 mt-1">
    <div class="col-12 col-xxl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-exclamation-triangle text-danger me-2"></i>Productos con stock bajo</strong>
                @can('products.view')
                    <a href="{{ route('products.index') }}" class="small">Ver todos</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($lowStockProducts as $product)
                        <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td class="cell-truncate" title="{{ $product->name }}">{{ $product->name }}</td>
                    <td class="text-center cell-num"><span class="badge text-bg-danger">{{ $product->stock }}</span></td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="bi bi-check-circle text-success"></i>
                                No hay productos con stock bajo.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xxl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-arrow-left-right me-2"></i>Movimientos recientes</strong>
                @can('movements.view')
                    <a href="{{ route('movimientos.index') }}" class="small">Ver historial</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Fecha</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($recentMovements as $movement)
                        <tr>
                            <td>
                                @if ($movement->isEntrada())
                                    <span class="badge text-bg-success">Entrada</span>
                                @else
                                    <span class="badge text-bg-warning">Salida</span>
                                @endif
                            </td>
                            <td class="cell-truncate" title="{{ $movement->product?->name }}">{{ $movement->product?->name ?? '—' }}</td>
                            <td class="text-center cell-num">{{ $movement->quantity }}</td>
                            <td class="text-center cell-num">{{ $movement->date->format('d/m/Y') }}</td>
                            <td>{{ $movement->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-arrow-left-right"></i>
                                Aún no hay movimientos registrados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
