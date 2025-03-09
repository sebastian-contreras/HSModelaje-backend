<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\EstablecimientosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\ModelosController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::prefix('personas')->group(function () {
    // Obtener todas las personas
    Route::get('/', [PersonaController::class, 'index'])->name('personas.index');
    // Crear una nueva persona
    Route::post('/', [PersonaController::class, 'store'])->name('personas.store');
    // Obtener una persona específica
    Route::get('/{id}', [PersonaController::class, 'show'])->name('personas.show');
    // Actualizar una persona específica
    Route::put('/{id}', [PersonaController::class, 'update'])->name('personas.update');
    // Eliminar una persona específica
    Route::delete('/{id}', [PersonaController::class, 'destroy'])->name('personas.destroy');
});


Route::prefix('cajas')->group(function () {
    // Obtener todas las cajas
    Route::get('/', [CajaController::class, 'index'])->name('cajas.index');
    // Crear una nueva Caja
    Route::post('/', [CajaController::class, 'store'])->name('cajas.store');
    // Obtener una Caja específica
    Route::get('/{id}', [CajaController::class, 'show'])->name('cajas.show');
    // Actualizar una Caja específica
    Route::put('/{caja}', [CajaController::class, 'update'])->name('cajas.update');
    // Eliminar una Caja específica
    Route::delete('/{caja}', [CajaController::class, 'destroy'])->name('cajas.destroy');
});


Route::prefix('usuarios')->group(function () {
    // Obtener todas las usuarios
    Route::get('/', [UsuariosController::class, 'index'])->name('usuarios.index');
    // Crear una nueva usuarios
    Route::post('/', [UsuariosController::class, 'store'])->name('usuarios.store');
    // Obtener una usuarios específica
    Route::get('/{IdUsuario}', [UsuariosController::class, 'show'])->name('usuarios.show');
    // Actualizar una usuarios específica
    Route::put('/{IdUsuario}', [UsuariosController::class, 'update'])->name('usuarios.update');
    // Eliminar una usuarios específica
    Route::delete('/{IdUsuario}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

    Route::post('/darbaja/{IdUsuario}', [UsuariosController::class, 'darBaja'])->name('usuarios.darBaja');
    Route::post('/activar/{IdUsuario}', [UsuariosController::class, 'activar'])->name('usuarios.activar');
});

Route::prefix('establecimientos')->group(function () {
    // Obtener todas las usuarios
    Route::get('/', [EstablecimientosController::class, 'index'])->name('establecimientos.index');
    Route::get('/busqueda', [EstablecimientosController::class, 'busqueda'])->name('establecimientos.busqueda');
    // Crear una nueva establecimientos
    Route::post('/', [EstablecimientosController::class, 'store'])->name('establecimientos.store');
    // Obtener una establecimientos específica
    Route::get('/{IdEstablecimiento}', [EstablecimientosController::class, 'show'])->name('establecimientos.show');
    // Actualizar una establecimientos específica
    Route::put('/{IdEstablecimiento}', [EstablecimientosController::class, 'update'])->name('establecimientos.update');
    // Eliminar una establecimientos específica
    Route::delete('/{IdEstablecimiento}', [EstablecimientosController::class, 'destroy'])->name('establecimientos.destroy');

    Route::post('/darbaja/{IdEstablecimiento}', [EstablecimientosController::class, 'darBaja'])->name('establecimientos.darBaja');
    Route::post('/activar/{IdEstablecimiento}', [EstablecimientosController::class, 'activar'])->name('establecimientos.activar');
});


Route::prefix('eventos')->group(function () {
    // Obtener todas los eventos
    Route::get('/', [EventosController::class, 'index'])->name('eventos.index');
    Route::get('/show/{IdEvento}', [EventosController::class, 'dame'])->name('eventos.dame');
    Route::get('/busqueda', [EventosController::class, 'busqueda'])->name('eventos.busqueda');
    // Crear una nueva eventos
    Route::post('/', [EventosController::class, 'store'])->name('eventos.store');
    // Obtener una eventos específica
    Route::get('/{IdEvento}', [EventosController::class, 'show'])->name('eventos.show');
    // Actualizar una eventos específica
    Route::put('/{IdEvento}', [EventosController::class, 'update'])->name('eventos.update');
    // Eliminar una eventos específica
    Route::delete('/{IdEvento}', [EventosController::class, 'destroy'])->name('eventos.destroy');

    Route::post('/darbaja/{IdEvento}', [EventosController::class, 'darBaja'])->name('eventos.darBaja');
    Route::post('/activar/{IdEvento}', [EventosController::class, 'activar'])->name('eventos.activar');
    Route::post('/finalizar/{IdEvento}', [EventosController::class, 'finalizar'])->name('eventos.finalizar');
});


Route::prefix('modelos')->group(function () {
    // Obtener todas los modelos
    Route::get('/', [ModelosController::class, 'index'])->name('modelos.index');
    Route::get('/show/{IdModelo}', [ModelosController::class, 'dame'])->name('modelos.dame');
    Route::get('/busqueda', [ModelosController::class, 'busqueda'])->name('modelos.busqueda');
    // Crear una nueva modelos
    Route::post('/', [ModelosController::class, 'store'])->name('modelos.store');
    // Obtener una modelos específica
    Route::get('/{IdModelo}', [ModelosController::class, 'show'])->name('modelos.show');
    // Actualizar una modelos específica
    Route::put('/{IdModelo}', [ModelosController::class, 'update'])->name('modelos.update');
    // Eliminar una modelos específica
    Route::delete('/{IdModelo}', [ModelosController::class, 'destroy'])->name('modelos.destroy');

    Route::post('/darbaja/{IdModelo}', [ModelosController::class, 'darBaja'])->name('modelos.darBaja');
    Route::post('/activar/{IdModelo}', [ModelosController::class, 'activar'])->name('modelos.activar');
});


Route::prefix('gastos')->group(function () {
    // Obtener todas los modelos
    Route::get('/busqueda', [GastosController::class, 'busqueda'])->name('modelos.busqueda');
    Route::get('/{IdEvento}', [GastosController::class, 'index'])->name('modelos.index');
    Route::get('/show/{IdGasto}', [GastosController::class, 'dame'])->name('modelos.dame');
    // Crear una nueva modelos
    Route::post('/', [GastosController::class, 'store'])->name('modelos.store');
    // Obtener una modelos específica
    Route::get('/{IdGasto}', [GastosController::class, 'show'])->name('modelos.show');
    // Actualizar una modelos específica
    Route::put('/{IdGasto}', [GastosController::class, 'update'])->name('modelos.update');
    // Eliminar una modelos específica
    Route::delete('/{IdGasto}', [GastosController::class, 'destroy'])->name('modelos.destroy');

});
