@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Usuarios</h1>
        <p class="text-secondary mb-0">Gestión de usuarios y roles del sistema (RF-02).</p>
    </div>
    @can('users.create')
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Registrar usuario
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
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th class="text-center">Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="text-center cell-code">{{ $user->id }}</td>
                    <td class="cell-truncate" title="{{ $user->name }}"><span class="fw-semibold">{{ $user->name }}</span></td>
                    <td class="cell-code">{{ $user->username }}</td>
                    <td class="cell-truncate" title="{{ $user->email }}">{{ $user->email }}</td>
                    <td class="text-center">
                        @if ($user->role?->slug === 'admin')
                            <span class="badge text-bg-primary">{{ $user->role?->name }}</span>
                        @elseif ($user->role?->slug === 'encargado')
                            <span class="badge text-bg-success">{{ $user->role?->name }}</span>
                        @else
                            <span class="badge text-bg-secondary">{{ $user->role?->name ?? 'Sin rol' }}</span>
                        @endif
                    </td>
                    <td class="text-center cell-actions">
                        @can('users.update')
                            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('users.delete')
                            @if ($user->id !== auth()->id())
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                        data-delete-url="{{ route('usuarios.destroy', $user) }}"
                                        data-delete-name="{{ $user->name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-people"></i>
                        No hay usuarios registrados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
