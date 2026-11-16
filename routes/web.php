<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas web
|--------------------------------------------------------------------------
|
| Todas las rutas del sistema están detrás de la autenticación y del
| middleware "permission:..." que valida el rol del usuario (RNF-04).
|
*/

Route::get('/', fn () => redirect()->route('dashboard'));

// Selector de idioma (español, inglés y francés). Disponible también sin sesión.
Route::get('/idioma/{locale}', function (string $locale) {
    abort_unless(array_key_exists($locale, config('app.available_locales')), 404);

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

// Invitados (sin sesión)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    // Recuperación de contraseña: envía por correo un enlace para restablecerla.
    Route::get('/contrasena/olvide', [ForgotPasswordController::class, 'create'])
        ->name('password.forgot');
    Route::post('/contrasena/olvide', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.forgot.store');

    // Verificación de correo: formulario público para pedir el enlace.
    Route::get('/correo/verificar/solicitud', [EmailVerificationController::class, 'create'])
        ->name('verification.request');
    Route::post('/correo/verificar/solicitud', [EmailVerificationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('verification.request.store');

    // Registro público de nuevos usuarios (rol por defecto: consultor).
    Route::get('/registro', [RegisterController::class, 'create'])
        ->name('register');
    Route::post('/registro', [RegisterController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('register.store');
});

// El enlace del correo abre este formulario; es público a propósito para
// que funcione aunque haya una sesión abierta (el token es de un solo uso).
Route::get('/contrasena/restablecer/{token}', [ResetPasswordController::class, 'create'])
    ->name('password.reset');
Route::post('/contrasena/restablecer/{token}', [ResetPasswordController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('password.reset.store');

// Usuarios autenticados: el logout y la verificación de correo son las
// únicas vistas disponibles con el correo todavía sin verificar.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Verificación de correo (RF/RNF).
    Route::get('/correo/verificar/aviso', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/correo/verificar/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/correo/verificar/reenviar', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:5,1')
        ->name('verification.resend');
});

// Sin el correo verificado no hay acceso a ninguna vista del sistema:
// el middleware "verified" redirige a verification.notice.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // Productos (RF-03, RF-11)
    Route::middleware('permission:products.view')->group(function () {
        Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
    });
    Route::get('/productos/crear', [ProductController::class, 'create'])
        ->middleware('permission:products.create')
        ->name('products.create');
    Route::post('/productos', [ProductController::class, 'store'])
        ->middleware('permission:products.create')
        ->name('products.store');
    Route::get('/productos/{product}/editar', [ProductController::class, 'edit'])
        ->middleware('permission:products.update')
        ->name('products.edit');
    Route::put('/productos/{product}', [ProductController::class, 'update'])
        ->middleware('permission:products.update')
        ->name('products.update');
    Route::delete('/productos/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:products.delete')
        ->name('products.destroy');

    // Categorías (RF-04)
    Route::get('/categorias', [CategoryController::class, 'index'])
        ->middleware('permission:categories.view')
        ->name('categories.index');
    Route::get('/categorias/crear', [CategoryController::class, 'create'])
        ->middleware('permission:categories.create')
        ->name('categories.create');
    Route::post('/categorias', [CategoryController::class, 'store'])
        ->middleware('permission:categories.create')
        ->name('categories.store');
    Route::get('/categorias/{category}/editar', [CategoryController::class, 'edit'])
        ->middleware('permission:categories.update')
        ->name('categories.edit');
    Route::put('/categorias/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:categories.update')
        ->name('categories.update');
    Route::delete('/categorias/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete')
        ->name('categories.destroy');

    // Entradas (RF-05)
    Route::middleware('permission:entries.view')->group(function () {
        Route::get('/entradas', [EntradaController::class, 'index'])->name('entradas.index');
    });
    Route::get('/entradas/crear', [EntradaController::class, 'create'])
        ->middleware('permission:entries.create')
        ->name('entradas.create');
    Route::post('/entradas', [EntradaController::class, 'store'])
        ->middleware('permission:entries.create')
        ->name('entradas.store');
    Route::get('/entradas/{movement}/editar', [EntradaController::class, 'edit'])
        ->middleware('permission:entries.update')
        ->name('entradas.edit');
    Route::put('/entradas/{movement}', [EntradaController::class, 'update'])
        ->middleware('permission:entries.update')
        ->name('entradas.update');
    Route::delete('/entradas/{movement}', [EntradaController::class, 'destroy'])
        ->middleware('permission:entries.delete')
        ->name('entradas.destroy');

    // Salidas (RF-06, RF-08)
    Route::middleware('permission:exits.view')->group(function () {
        Route::get('/salidas', [SalidaController::class, 'index'])->name('salidas.index');
    });
    Route::get('/salidas/crear', [SalidaController::class, 'create'])
        ->middleware('permission:exits.create')
        ->name('salidas.create');
    Route::post('/salidas', [SalidaController::class, 'store'])
        ->middleware('permission:exits.create')
        ->name('salidas.store');
    Route::get('/salidas/{movement}/editar', [SalidaController::class, 'edit'])
        ->middleware('permission:exits.update')
        ->name('salidas.edit');
    Route::put('/salidas/{movement}', [SalidaController::class, 'update'])
        ->middleware('permission:exits.update')
        ->name('salidas.update');
    Route::delete('/salidas/{movement}', [SalidaController::class, 'destroy'])
        ->middleware('permission:exits.delete')
        ->name('salidas.destroy');

    // Movimientos (RF-10)
    Route::get('/movimientos', [MovementController::class, 'index'])
        ->middleware('permission:movements.view')
        ->name('movimientos.index');

    // Usuarios (RF-02)
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    });
    Route::get('/usuarios/crear', [UserController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('usuarios.create');
    Route::post('/usuarios', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('usuarios.store');
    // El perfil se declara después de /usuarios/crear para no capturar esa ruta.
    // Cualquier usuario, sea cual sea su rol, puede ver y editar su propio perfil;
    // el acceso a perfiles de otros usuarios se valida en el controlador (users.*)
    // sin middleware de permiso para poder distinguir "propio" de "ajeno".
    Route::get('/usuarios/{user}', [UserController::class, 'show'])
        ->name('usuarios.show');
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])
        ->name('usuarios.edit');
    Route::get('/usuarios/{user}/contrasena', [UserController::class, 'editPassword'])
        ->name('usuarios.password');
    Route::put('/usuarios/{user}/contrasena', [UserController::class, 'updatePassword'])
        ->name('usuarios.password.update');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->name('usuarios.update');
    // Igual que el perfil: cada usuario puede eliminarse a sí mismo;
    // eliminar a otros exige users.delete y se valida en el controlador.
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->name('usuarios.destroy');

    // Reportes
    Route::get('/reportes', [ReportController::class, 'index'])
        ->middleware('permission:reports.view')
        ->name('reportes.index');
});
