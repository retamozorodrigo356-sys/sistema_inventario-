<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Sistema de Inventario</title>

    <!-- Bootstrap 5 + iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

<div class="app-wrapper">

    {{-- ===== SIDEBAR ===== --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-box-seam-fill"></i>
            <span>Sistema de Inventario</span>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-label">Menú principal</li>

            @can('dashboard.view')
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                    </a>
                </li>
            @endcan

            @can('products.view')
                <li>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i><span>Productos</span>
                    </a>
                </li>
            @endcan

            @can('categories.view')
                <li>
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i><span>Categorías</span>
                    </a>
                </li>
            @endcan

            @can('entries.view')
                <li>
                    <a href="{{ route('entradas.index') }}" class="{{ request()->routeIs('entradas.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-in-down"></i><span>Entradas</span>
                    </a>
                </li>
            @endcan

            @can('exits.view')
                <li>
                    <a href="{{ route('salidas.index') }}" class="{{ request()->routeIs('salidas.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-up"></i><span>Salidas</span>
                    </a>
                </li>
            @endcan

            @can('movements.view')
                <li>
                    <a href="{{ route('movimientos.index') }}" class="{{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i><span>Movimientos</span>
                    </a>
                </li>
            @endcan

            @can('reports.view')
                <li>
                    <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i><span>Reportes</span>
                    </a>
                </li>
            @endcan

            @can('users.view')
                <li class="sidebar-label mt-3">Administración</li>
                <li>
                    <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i><span>Usuarios</span>
                    </a>
                </li>
            @endcan
        </ul>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-left"></i><span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ===== CONTENIDO ===== --}}
    <div class="app-content">

        <header class="topbar">
            <button class="btn btn-light btn-sm d-lg-none" id="sidebarToggle" type="button" aria-label="Menú">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="topbar-title d-none d-lg-block">@yield('title', 'Dashboard')</div>

            <div class="dropdown">
                <a class="dropdown-toggle topbar-user" data-bs-toggle="dropdown" href="#" role="button">
                    <span class="avatar"><i class="bi bi-person-fill"></i></span>
                    <span class="d-none d-md-inline">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small class="d-block text-secondary">{{ auth()->user()->role?->name ?? 'Sin rol' }}</small>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><span class="dropdown-item-text small text-secondary">Sesión iniciada como<br><strong>{{ auth()->user()->email }}</strong></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-left me-2"></i>Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="page-container">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>

{{-- Modal genérico de confirmación de eliminación --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de eliminar <strong id="deleteItemName">este registro</strong>?
                <p class="text-secondary small mb-0 mt-2">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Menú lateral en móvil
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle')?.addEventListener('click', () => sidebar.classList.toggle('open'));
    overlay?.addEventListener('click', () => sidebar.classList.remove('open'));

    // Modal de eliminación reutilizable
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-delete-url]');
        if (!trigger) return;

        document.getElementById('deleteForm').action = trigger.dataset.deleteUrl;
        document.getElementById('deleteItemName').textContent = trigger.dataset.deleteName || 'este registro';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
</script>
@stack('scripts')
</body>
</html>
