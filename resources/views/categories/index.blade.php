@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Categorías</h1>
        <p class="text-secondary mb-0">Agrupa los productos por categoría (RF-04).</p>
    </div>
    @can('categories.create')
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Registrar categoría
        </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Productos</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td class="text-center cell-code">{{ $category->id }}</td>
                    <td><span class="fw-semibold">{{ $category->name }}</span></td>
                    <td class="cell-truncate" title="{{ $category->description }}">{{ $category->description ?? '—' }}</td>
                    <td class="text-center cell-num">
                        <span class="badge text-bg-light border">{{ $category->products_count }}</span>
                    </td>
                    <td class="text-center cell-actions">
                        @can('categories.update')
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('categories.delete')
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                    data-delete-url="{{ route('categories.destroy', $category) }}"
                                    data-delete-name="{{ $category->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">
                        <i class="bi bi-tags"></i>
                        No hay categorías registradas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($categories->hasPages())
        <div class="card-footer bg-white">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
