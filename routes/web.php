<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Páginas públicas
|--------------------------------------------------------------------------
*/

Route::view('/', 'pages.home')->name('home');
Route::view('/quienes-somos', 'pages.quienes-somos')->name('quienes-somos');
Route::view('/mision-vision', 'pages.mision-vision')->name('mision-vision');
Route::view('/ubicacion', 'pages.ubicacion')->name('ubicacion');
Route::view('/contacto', 'pages.contacto')->name('contacto');

/*
|--------------------------------------------------------------------------
| Solo invitados
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('/registro', [RegisterController::class, 'register'])
        ->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Usuarios autenticados
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'redirect'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard de administrador
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/administrador/dashboard',
        [DashboardController::class, 'administrador']
    )
        ->middleware('role:administrador')
        ->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard de cliente
    |--------------------------------------------------------------------------
    */

    Route::get('/cliente/dashboard', [DashboardController::class, 'cliente'])
        ->middleware('role:cliente')
        ->name('cliente.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard de empleado
    |--------------------------------------------------------------------------
    */

    Route::get('/empleado/dashboard', [DashboardController::class, 'empleado'])
        ->middleware('role:empleado')
        ->name('empleado.dashboard');

    /*
    |--------------------------------------------------------------------------
    | CRUD de usuarios: gerente y administrador
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:gerente,administrador')->group(function () {
        Route::resource('usuarios', UserController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Productos, categorías y ventas
    |--------------------------------------------------------------------------
    */

    Route::resource('productos', ProductoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('ventas', VentaController::class);

    /*
    |--------------------------------------------------------------------------
    | Dashboard de gerente
    |--------------------------------------------------------------------------
    */

    Route::get('/gerente/dashboard', [DashboardController::class, 'gerente'])
        ->middleware('role:gerente')
        ->name('gerente.dashboard');
});