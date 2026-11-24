<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.brand_web') }} · IESTP "César A. Guardia Mayorga"</title>

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #0f172a;
            --primary-navy: #0f3460;
            --accent-crimson: #e94560;
            --accent-gold: #f59e0b;
            --accent-blue: #0284c7;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Navbar */
        .public-navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #0f3460 55%, #16213e 100%);
            color: #ffffff;
            padding: 90px 0 110px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(233, 69, 96, 0.25) 0%, rgba(233, 69, 96, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.25) 0%, rgba(2, 132, 199, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-badge {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffdd59;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hero-title {
            font-size: 2.85rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .hero-section { padding: 60px 0 80px 0; }
        }

        .hero-lead {
            font-size: 1.15rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .btn-enter {
            background: linear-gradient(135deg, #e94560 0%, #d63031 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 14px 34px;
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 25px rgba(233, 69, 96, 0.35);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-enter:hover {
            background: linear-gradient(135deg, #ff5e78 0%, #e94560 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(233, 69, 96, 0.45);
        }

        .btn-outline-custom {
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            padding: 13px 28px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-color: #ffffff;
        }

        /* Metric Bar */
        .metric-bar {
            margin-top: -45px;
            position: relative;
            z-index: 10;
        }

        .metric-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.12);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Modules Section */
        .section-title-badge {
            color: #0284c7;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .section-heading {
            font-size: 2.1rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .module-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .module-card:hover {
            transform: translateY(-6px);
            border-color: #cbd5e1;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        }

        .module-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 20px;
        }

        /* Institutional banner */
        .institution-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 20px;
            color: #ffffff;
            padding: 45px 35px;
            position: relative;
            overflow: hidden;
        }

        /* Footer */
        .public-footer {
            background: #0b1120;
            color: #94a3b8;
            padding: 50px 0 30px 0;
            border-top: 1px solid #1e293b;
        }
    </style>
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <nav class="navbar navbar-expand-lg navbar-dark public-navbar sticky-top py-2">
        <div class="container">
            {{-- Brand con Logo --}}
            <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('home') }}">
                <img src="{{ asset('images/logo-instituto.png') }}" alt="Logo Instituto" style="height: 48px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));">
                <div class="d-flex flex-column">
                    <span class="fw-bold lh-1 text-white" style="font-size: 1.05rem;">{{ __('app.brand_web') }}</span>
                    <small class="text-warning fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">IESTP "César A. Guardia Mayorga"</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active fw-medium px-3" href="#inicio"><i class="bi bi-house-door me-1"></i> Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium px-3 text-light" href="#modulos"><i class="bi bi-grid me-1"></i> Módulos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium px-3 text-light" href="#institucion"><i class="bi bi-mortarboard me-1"></i> Institución</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    {{-- Logo Carrera en Navbar --}}
                    <img src="{{ asset('images/logo-carrera.png') }}" alt="Logo Carrera" class="d-none d-lg-block me-2" style="height: 45px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));" title="Arquitectura de Plataformas y Servicios TI">

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-warning fw-bold px-3 py-2">
                            <i class="bi bi-speedometer2 me-1"></i> Ir al Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-enter py-2 px-3">
                            <i class="bi bi-box-arrow-in-right"></i> Ingresar al Sistema
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section" id="inicio">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-badge mb-3">
                        <i class="bi bi-shield-check text-warning"></i> Plataforma Institucional de Producción v1.0
                    </div>
                    <h1 class="hero-title mb-3">
                        Control Integral y Gestión de <span style="color: #ffdd59;">Inventario Web</span>
                    </h1>
                    <p class="hero-lead mb-4">
                        Sistema informático institucional para la administración de bienes, control de existencias en tiempo real, registro de entradas, asignación de salidas y auditoría de kárdex del almacén central.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-enter">
                                <i class="bi bi-speedometer2"></i> Acceder al Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-enter">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión en el Sistema
                            </a>
                            <a href="#modulos" class="btn-outline-custom">
                                <i class="bi bi-info-circle"></i> Conocer Módulos
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Tarjeta interactiva institucional --}}
                <div class="col-lg-5 text-center">
                    <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px);">
                        <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
                            <img src="{{ asset('images/logo-instituto.png') }}" alt="Instituto" style="height: 90px; width: auto; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));">
                            <img src="{{ asset('images/logo-carrera.png') }}" alt="Carrera" style="height: 90px; width: auto; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));">
                        </div>
                        <h5 class="fw-bold text-white mb-1">IESTP "César A. Guardia Mayorga"</h5>
                        <p class="text-warning small mb-3">Coracora — Parinacochas — Ayacucho</p>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-light w-100 fw-semibold mt-2">
                                <i class="bi bi-key me-1"></i> Ir al Formulario de Acceso
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-warning w-100 fw-semibold mt-2">
                                <i class="bi bi-speedometer2 me-1"></i> Ir al Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== BARRA DE MÉTRICAS / PILARES ===== --}}
    <div class="container metric-bar">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="metric-card d-flex align-items-center gap-3">
                    <div class="metric-icon bg-primary-subtle text-primary">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Control de Stock en Tiempo Real</h6>
                        <small class="text-muted">Actualización automática con cada movimiento.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card d-flex align-items-center gap-3">
                    <div class="metric-icon bg-success-subtle text-success">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Trazabilidad Total (Kárdex)</h6>
                        <small class="text-muted">Registro histórico inmutable de entradas y salidas.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card d-flex align-items-center gap-3">
                    <div class="metric-icon bg-danger-subtle text-danger">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Seguridad por Roles (RBAC)</h6>
                        <small class="text-muted">Acceso restringido: Admin, Almacén y Consultor.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SECCIÓN DE MÓDULOS ===== --}}
    <section class="py-5 mt-4" id="modulos">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-title-badge">Capacidades del Sistema</span>
                <h2 class="section-heading mt-1">Módulos de Gestión Integrados</h2>
                <p class="text-muted mx-auto" style="max-width: 650px;">
                    Estructura modular diseñada para optimizar los procesos de almacenamiento, custodia de insumos y transparencia administrativa.
                </p>
            </div>

            <div class="row g-4">
                {{-- Módulo 1: Productos --}}
                <div class="col-md-6 col-lg-4">
                    <div class="module-card">
                        <div class="module-icon-wrap bg-primary-subtle text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Catálogo de Productos</h5>
                        <p class="text-muted small mb-0 flex-grow-1">
                            Administración de bienes con códigos únicos, umbrales de stock mínimo, unidades de medida y alertas automáticas de reposición crítica.
                        </p>
                    </div>
                </div>

                {{-- Módulo 2: Categorías --}}
                <div class="col-md-6 col-lg-4">
                    <div class="module-card">
                        <div class="module-icon-wrap bg-warning-subtle text-warning">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Familias y Categorías</h5>
                        <p class="text-muted small mb-0 flex-grow-1">
                            Clasificación jerárquica de materiales, herramientas, papelería y equipos para facilitar la búsqueda y la auditoría segmentada.
                        </p>
                    </div>
                </div>

                {{-- Módulo 3: Entradas --}}
                <div class="col-md-6 col-lg-4">
                    <div class="module-card">
                        <div class="module-icon-wrap bg-success-subtle text-success">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Registro de Entradas</h5>
                        <p class="text-muted small mb-0 flex-grow-1">
                            Recepción de compras y donaciones con registro del proveedor, comprobante y suma inmediata al stock disponible del almacén.
                        </p>
                    </div>
                </div>

                {{-- Módulo 4: Salidas --}}
                <div class="col-md-6 col-lg-4">
                    <div class="module-card">
                        <div class="module-icon-wrap bg-danger-subtle text-danger">
                            <i class="bi bi-box-arrow-up"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Despacho de Salidas</h5>
                        <p class="text-muted small mb-0 flex-grow-1">
                            Egreso de insumos hacia aulas, talleres y oficinas con validación automática de existencias para prevenir desabastecimiento.
                        </p>
                    </div>
                </div>

                {{-- Módulo 5: Kárdex y Movimientos --}}
                <div class="col-md-6 col-lg-4">
                    <div class="module-card">
                        <div class="module-icon-wrap bg-info-subtle text-info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Historial de Kárdex</h5>
                        <p class="text-muted small mb-0 flex-grow-1">
                            Registro cronológico inalterable de cada operación, detallando usuario responsable, cantidades, fecha exacta y observaciones.
                        </p>
                    </div>
                </div>

                {{-- Módulo 6: Reportes y Exportación --}}
                <div class="col-md-6 col-lg-4">
                    <div class="module-card">
                        <div class="module-icon-wrap bg-secondary-subtle text-dark">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Reportes e Informes</h5>
                        <p class="text-muted small mb-0 flex-grow-1">
                            Generación de balances de stock, productos con existencia crítica y exportación inmediata en formato PDF y hojas de cálculo Excel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== BANNER INSTITUCIONAL ===== --}}
    <section class="py-5 bg-white border-top border-bottom" id="institucion">
        <div class="container py-2">
            <div class="institution-banner">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-warning text-dark fw-bold mb-2">Entidad Educativa Pública</span>
                        <h3 class="fw-bold text-white mb-2">Instituto de Educación Superior Tecnológico Público "César A. Guardia Mayorga"</h3>
                        <p class="text-light opacity-75 mb-3" style="max-width: 620px;">
                            Comprometidos con la excelencia académica y la formación tecnológica en la provincia de Parinacochas, Ayacucho.
                        </p>
                        <div class="d-flex flex-wrap gap-4 text-light small">
                            <div><i class="bi bi-geo-alt-fill text-warning me-1"></i> Coracora, Ayacucho - Perú</div>
                            <div><i class="bi bi-laptop text-warning me-1"></i> Programa: Arquitectura de Plataformas y Servicios TI</div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Acceder al Sistema
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow">
                                <i class="bi bi-speedometer2 me-1"></i> Ir al Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="public-footer">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start mb-2">
                        <img src="{{ asset('images/logo-instituto.png') }}" alt="IESTP" style="height: 38px;">
                        <img src="{{ asset('images/logo-carrera.png') }}" alt="APSTI" style="height: 38px;">
                        <span class="text-white fw-bold">{{ __('app.brand_web') }}</span>
                    </div>
                    <small class="d-block text-secondary">
                        Desarrollado para el IESTP "César A. Guardia Mayorga" · Coracora, Parinacochas, Ayacucho.
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex gap-3 justify-content-center justify-content-md-end mb-2">
                        <a href="{{ route('login') }}" class="text-light text-decoration-none small"><i class="bi bi-key me-1"></i> Iniciar Sesión</a>
                    </div>
                    <small class="text-secondary">© {{ date('Y') }} Sistema de Inventario Web. Todos los derechos reservados.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
