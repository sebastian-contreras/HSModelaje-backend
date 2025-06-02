<?php

use App\Events\TestEvent;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\EntradasController;
use App\Http\Controllers\EstablecimientosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\JuecesController;
use App\Http\Controllers\MetricasController;
use App\Http\Controllers\ModelosController;
use App\Http\Controllers\ParticipantesController;
use App\Http\Controllers\PatrocinadoresController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ZonasController;
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
Route::get('/test', function () {
    Log::info('Probando el endpoint de test');
    broadcast(new TestEvent('Hola desde Reverb!'));
    return 'Evento enviado';
});

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
    Route::get('/{IdEstablecimiento}', [EstablecimientosController::class, 'dame'])->name('establecimientos.dame');
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
    Route::get('/busqueda', [GastosController::class, 'busqueda'])->name('gastos.busqueda');
    Route::get('/{IdEvento}', [GastosController::class, 'index'])->name('gastos.index');
    Route::get('/show/{IdGasto}', [GastosController::class, 'dame'])->name('gastos.dame');
    // Crear una nueva gastos
    Route::post('/', [GastosController::class, 'store'])->name('gastos.store');
    // Obtener una gastos específica
    Route::get('/{IdGasto}', [GastosController::class, 'show'])->name('gastos.show');
    // Actualizar una gastos específica
    Route::put('/{IdGasto}', [GastosController::class, 'update'])->name('gastos.update');
    // Eliminar una gastos específica
    Route::delete('/{IdGasto}', [GastosController::class, 'destroy'])->name('gastos.destroy');

});


Route::prefix('patrocinadores')->group(function () {
    // Obtener todas los patrocinadores
    Route::get('/busqueda', [PatrocinadoresController::class, 'busqueda'])->name('patrocinadores.busqueda');
    Route::get('/{IdEvento}', [PatrocinadoresController::class, 'index'])->name('patrocinadores.index');
    Route::get('/show/{IdPatrocinador}', [PatrocinadoresController::class, 'dame'])->name('patrocinadores.dame');
    // Crear una nueva patrocinadores
    Route::post('/', [PatrocinadoresController::class, 'store'])->name('patrocinadores.store');
    // Obtener una patrocinadores específica
    Route::get('/{IdPatrocinador}', [PatrocinadoresController::class, 'show'])->name('patrocinadores.show');
    // Actualizar una patrocinadores específica
    Route::put('/{IdPatrocinador}', [PatrocinadoresController::class, 'update'])->name('patrocinadores.update');
    // Eliminar una patrocinadores específica
    Route::delete('/{IdPatrocinador}', [PatrocinadoresController::class, 'destroy'])->name('patrocinadores.destroy');

});




Route::prefix('jueces')->group(function () {
    // Obtener todas los jueces
    Route::get('/busqueda', [JuecesController::class, 'busqueda'])->name('jueces.busqueda');
    Route::get('/{IdEvento}', [JuecesController::class, 'index'])->name('jueces.index');
    Route::get('/show/{IdJuez}', [JuecesController::class, 'dame'])->name('jueces.dame');
    // Crear una nueva jueces
    Route::post('/', [JuecesController::class, 'store'])->name('jueces.store');
    // Obtener una jueces específica
    Route::get('/{IdJuez}', [JuecesController::class, 'show'])->name('jueces.show');
    // Actualizar una jueces específica
    Route::put('/{IdJuez}', [JuecesController::class, 'update'])->name('jueces.update');
    // Eliminar una jueces específica
    Route::delete('/{IdJuez}', [JuecesController::class, 'destroy'])->name('jueces.destroy');

    Route::post('/darbaja/{IdJuez}', [JuecesController::class, 'darBaja'])->name('jueces.darBaja');
    Route::post('/activar/{IdJuez}', [JuecesController::class, 'activar'])->name('jueces.activar');
});

Route::prefix('zonas')->group(function () {
    // Obtener todas los zonas
    Route::get('/busqueda', [ZonasController::class, 'busqueda'])->name('zonas.busqueda');
    Route::get('/{IdEvento}', [ZonasController::class, 'index'])->name('zonas.index');
    Route::get('/show/{IdZona}', [ZonasController::class, 'dame'])->name('zonas.dame');
    // Crear una nueva zonas
    Route::post('/', [ZonasController::class, 'store'])->name('zonas.store');
    // Obtener una zonas específica
    Route::get('/{IdZona}', [ZonasController::class, 'show'])->name('zonas.show');
    // Actualizar una zonas específica
    Route::put('/{IdZona}', [ZonasController::class, 'update'])->name('zonas.update');
    // Eliminar una zonas específica
    Route::delete('/{IdZona}', [ZonasController::class, 'destroy'])->name('zonas.destroy');

    Route::post('/darbaja/{IdZona}', [ZonasController::class, 'darBaja'])->name('zonas.darBaja');
    Route::post('/activar/{IdZona}', [ZonasController::class, 'activar'])->name('zonas.activar');
});

Route::prefix('metricas')->group(function () {
    // Obtener todas los metricas
    Route::get('/busqueda', [MetricasController::class, 'busqueda'])->name('metricas.busqueda');
    Route::get('/{IdEvento}', [MetricasController::class, 'index'])->name('metricas.index');
    Route::get('/show/{IdMetrica}', [MetricasController::class, 'dame'])->name('metricas.dame');
    // Crear una nueva metricas
    Route::post('/', [MetricasController::class, 'store'])->name('metricas.store');
    // Obtener una metricas específica
    Route::get('/{IdMetrica}', [MetricasController::class, 'show'])->name('metricas.show');
    // Actualizar una metricas específica
    Route::put('/{IdMetrica}', [MetricasController::class, 'update'])->name('metricas.update');
    // Eliminar una metricas específica
    Route::delete('/{IdMetrica}', [MetricasController::class, 'destroy'])->name('metricas.destroy');

    Route::post('/darbaja/{IdMetrica}', [MetricasController::class, 'darBaja'])->name('metricas.darBaja');
    Route::post('/activar/{IdMetrica}', [MetricasController::class, 'activar'])->name('metricas.activar');
});



Route::prefix('entradas')->group(function () {
    // Obtener todas los entradas
    Route::get('/busqueda', [EntradasController::class, 'busqueda'])->name('entradas.busqueda');
    Route::get('/{IdEvento}', [EntradasController::class, 'index'])->name('entradas.index');
    Route::get('/show/{IdEntrada}', [EntradasController::class, 'dame'])->name('entradas.dame');
    // Crear una nueva entradas
    Route::post('/', [EntradasController::class, 'store'])->name('entradas.store');
    Route::post('/pasarela', [EntradasController::class, 'storePasarela'])->name('entradas.storePasarela');
    // Obtener una entradas específica
    Route::get('/{IdEntrada}', [EntradasController::class, 'show'])->name('entradas.show');
    // Actualizar una entradas específica
    Route::put('/{IdEntrada}', [EntradasController::class, 'update'])->name('entradas.update');
    // Eliminar una entradas específica
    Route::delete('/{IdEntrada}', [EntradasController::class, 'destroy'])->name('entradas.destroy');

    Route::post('/abonar/{IdEntrada}', [EntradasController::class, 'abonar'])->name('entradas.abonar');
    Route::post('/usar/{IdEntrada}', [EntradasController::class, 'usar'])->name('entradas.usar');
    Route::post('/rechazar/{IdEntrada}', [EntradasController::class, 'rechazar'])->name('entradas.rechazar');
});


Route::prefix('participantes')->group(function () {
    // Obtener todas los participantes
    Route::get('/busqueda', [ParticipantesController::class, 'busqueda'])->name('participantes.busqueda');
    // Crear una nueva participantes
    Route::post('/', [ParticipantesController::class, 'store'])->name('participantes.store');
    // Obtener una participantes específica
    Route::delete('/{IdParticipante}', [ParticipantesController::class, 'destroy'])->name('participantes.destroy');
});
