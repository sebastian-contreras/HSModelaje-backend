<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use DB;
use Illuminate\Http\Request;

class EventosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_evento(?)', [$pIncluyeBajas]);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los eventos.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        // Obtener los parámetros de la solicitud
        $pCadena = $request->input('pCadena', ''); // Valor por defecto ''
        $pIncluyeVotacion = $request->input('pIncluyeVotacion', null); // Valor por defecto 'N'
        $pFechaInicio = $request->input('pFechaInicio', null);
        $pFechaFinal = $request->input('pFechaFinal', null);
        $pEstado = $request->input('pEstado', 'A');

        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_buscar_evento(?,?,?,?,?,?,?)', [$pCadena,$pEstado, $pIncluyeVotacion,$pFechaInicio, $pFechaFinal ,$pOffset, $pCantidad]);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los eventos: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventoRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_evento(?, ?, ?,?,?)', [
            $request->Evento,
            $request->FechaProbableInicio,
            $request->FechaProbableFinal,
            $request->Votacion,
            $request->IdEstablecimiento,
        ]);


        return ResponseFormatter::success($result, 'Evento creado exitosamente.', 201);

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
    public function update(UpdateEventoRequest $request, int $IdEvento)
    {
        //
        $request->validated();
        $result = DB::select('CALL bsp_modifica_evento(?, ?, ?,?,?,?,?,?)', [
            $request->IdEvento,
            $request->Evento,
            $request->FechaProbableInicio,
            $request->FechaProbableFinal,
            $request->Votacion,
            null,
            null,
            $request->IdEstablecimiento,
        ]);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Evento modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdEvento)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_evento(?)', [$IdEvento]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el evento.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento borrado exitosamente.', 200);
    }


    public function darBaja(int $IdEvento)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_darbaja_evento(?)', [$IdEvento]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento dado de baja exitosamente.', 200);
    }

    public function activar(int $IdEvento)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_activar_evento(?)', [$IdEvento]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento activo exitosamente.', 200);
    }

    public function finalizar(int $IdEvento,Request $request)
    {

        $pFechaInicio = $request->input('pFechaInicio'); // Valor por defecto ''
        $pFechaFinal = $request->input('pFechaFinal'); // Valor por defecto 'N'


        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_finalizar_evento(?,?,?)', [$IdEvento, $pFechaInicio, $pFechaFinal]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento finalizado exitosamente.', 200);
    }
}
