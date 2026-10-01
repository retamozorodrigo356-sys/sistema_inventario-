<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="login-page">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-9 col-md-6 col-lg-5 col-xl-4">

            <div class="text-center text-white mb-4">
                <div class="login-logo mx-auto"><i class="bi bi-box-seam-fill"></i></div>
                <h1 class="h4 mt-3 mb-1">Sistema de Inventario Web</h1>
                <p class="mb-0 opacity-75">Administración de productos, stock y movimientos</p>
            </div>

            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Iniciar sesión</h2>

                    @if (session('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('login.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label">Usuario o correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="login" id="login" class="form-control"
                                       value="{{ old('login') }}" placeholder="admin" required autofocus>
                            </div>
                            @error('login')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control"
                                       placeholder="••••••••" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                            <label class="form-check-label small" for="remember">Recordar mi sesión</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body py-3 px-4">
                    <div class="small text-secondary mb-2"><strong>Usuarios de prueba</strong></div>
                    <ul class="list-unstyled small mb-0 text-secondary">
                        <li><i class="bi bi-dot"></i> admin / admin123 <span class="badge text-bg-primary">Administrador</span></li>
                        <li><i class="bi bi-dot"></i> almacen / almacen123 <span class="badge text-bg-success">Encargado</span></li>
                        <li><i class="bi bi-dot"></i> consultor / consultor123 <span class="badge text-bg-secondary">Consultor</span></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
