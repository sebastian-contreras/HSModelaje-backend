<?php

namespace App\Http\Controllers;

use App\Classes\Jueces;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreJuezRequest;
use App\Http\Requests\UpdateJuezRequest;
use App\Jobs\SendEmailJob;
use App\Mail\InvitacionJuezMail;
use App\Services\GestorJueces;
use DB;
use Illuminate\Http\Request;
use Log;

class JuecesController extends Controller
{
        protected $gestorJueces;

    public function __construct(GestorJueces $gestorJueces)
    {
        $this->gestorJueces = $gestorJueces;
    }

    /**
     * Display a listing of the resource.
     */

    public function dame(string $IdJuez)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        try {
            // Llamar al procedimiento almacenado
          $juez = new Jueces(['IdJuez' => $IdJuez]);
            $lista = $juez->Dame();
            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener el juez.', 500);
        }
    }

    public function dameToken(string $Token)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        try {
            // Llamar al procedimiento almacenado
  $juez = new Jueces(['Token' => $Token]);
            $lista = $juez->DamePorToken();
           
            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener el juez.', 500);
        }
    }
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'
        $pIdEvento = intval($request->input('pIdEvento')); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorJueces->ListarEnEvento($pIdEvento, $pIncluyeBajas);
            Log::info('Lista de jueces obtenida', ['lista' => $lista]);
            Log::info('pIdEvento', ['pIdEvento' => $pIdEvento]);
            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los jueces.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        $pIdEvento = $request->input('pIdEvento', null);
        $pDNI = $request->input('pDNI', null);
        $pApelName = $request->input('pApelName', null);
        $pEstado = $request->input('pEstado', null);
        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorJueces->BuscarEnEvento($pOffset, $pCantidad, $pIdEvento, $pDNI, $pApelName, $pEstado );  
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los jueces: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuezRequest $request)
    {
        //
        $request->validated();
        $juez = new Jueces($request->all());
        $result = $this->gestorJueces->Alta($juez);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        return ResponseFormatter::success($result, 'Juez creado exitosamente.', 201);

    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuezRequest $request, int $IdJuez)
    {
        //
        $request->validated();
        $data = $request->all();
        $data['IdJuez'] = $IdJuez;
        $juez = new Jueces($data);
        $result = $this->gestorJueces->Modifica($juez);



        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Juez modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdJuez)
    {
        // Llamar al procedimiento almacenado
        $result = $this->gestorJueces->Borra($IdJuez);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el juez.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Juez borrado exitosamente.', 200);
    }


    public function darBaja(int $IdJuez)
    {
        // Llamar al procedimiento almacenado
        $juez = new Jueces(['IdJuez' => $IdJuez]);
        $result = $juez->DarBaja();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Juez dado de baja exitosamente.', 200);
    }

    public function activar(int $IdJuez)
    {
        // Llamar al procedimiento almacenado
        $juez = new Jueces(['IdJuez' => $IdJuez]);
        $result = $juez->Activar();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Juez activo exitosamente.', 200);
    }

    public function invitar(int $IdJuez)
    {
        // Llamar al procedimiento almacenado
        $juez = new Jueces(['IdJuez' => $IdJuez]);
        $result = $juez->Dame();



        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el juez.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        SendEmailJob::dispatch($result[0]->Correo, new InvitacionJuezMail($result));
        return ResponseFormatter::success(null, 'Se envio el correo de manera exitosa.', 200);
    }

}
