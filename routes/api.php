<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
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

Route::post('/login',[AuthController::class,'login'])->name('login');

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
});