@extends('layouts.app')

@section('title', 'Salidas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Salidas de productos</h1>
        <p class="text-secondary mb-0">Historial de egresos del inventario (RF-06).</p>
    </div>
    @can('exits.create')
        <a href="{{ route('salidas.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg me-1"></i>Registrar salida
        </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Fecha</th>
                    <th>Observación</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td class="text-center cell-code">{{ $movement->id }}</td>
                    <td class="cell-truncate" title="{{ $movement->product?->name }}">
                        <span class="fw-semibold">{{ $movement->product?->name ?? '—' }}</span>
                        <span class="cell-sub">{{ $movement->product?->code }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-bg-warning text-dark cell-num">-{{ $movement->quantity }}</span>
                    </td>
                    <td class="text-center cell-num">{{ $movement->date->format('d/m/Y') }}</td>
                    <td>{{ $movement->observation ?? '—' }}</td>
                    <td>{{ $movement->user?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-box-arrow-up"></i>
                        No hay salidas registradas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($movements->hasPages())
        <div class="card-footer bg-white">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
