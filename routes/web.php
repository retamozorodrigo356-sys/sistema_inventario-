<?php

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

// Invitados (sin sesión)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

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
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])
        ->middleware('permission:users.update')
        ->name('usuarios.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('usuarios.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('usuarios.destroy');

    // Reportes
    Route::get('/reportes', [ReportController::class, 'index'])
        ->middleware('permission:reports.view')
        ->name('reportes.index');
});
