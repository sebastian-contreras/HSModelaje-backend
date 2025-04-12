<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreParticipanteRequest;
use DB;
use Illuminate\Http\Request;

class ParticipantesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function busqueda(Request $request)
    {
        $pIdEvento = $request->input('pIdEvento', null);
        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_participantes(?,?,?)', [$pIdEvento, $pOffset, $pCantidad]);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los participantes: ' . $e->getMessage(), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticipanteRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_participante( ?, ?,?)', [
            $request->IdEvento,
            $request->IdModelo,
            $request->Promotor,
        ]);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        return ResponseFormatter::success($result, 'Participante creado exitosamente.', 201);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdParticipante)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_participante(?)', [$IdParticipante]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el participante.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Participante borrado exitosamente.', 200);
    }
}
