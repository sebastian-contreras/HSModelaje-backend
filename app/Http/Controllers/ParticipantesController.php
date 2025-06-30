<?php

namespace App\Http\Controllers;

use App\Classes\Participantes;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreParticipanteRequest;
use App\Services\GestorParticipantes;
use DB;
use Illuminate\Http\Request;

class ParticipantesController extends Controller
{
        protected $gestorParticipantes;

    public function __construct(GestorParticipantes $gestorParticipantes)
    {
        $this->gestorParticipantes = $gestorParticipantes;
    }

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
            $lista = $this->gestorParticipantes->Listar($pIdEvento, $pOffset, $pCantidad);
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
        $participante = new Participantes($request->all());
        $result = $this->gestorParticipantes->Alta($participante);

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
        try {
            $participante = new Participantes(['IdParticipante' => $id]);
            $result = $participante->Dame();
            return ResponseFormatter::success($result);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error al obtener el participante.', 500);
        }
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
        $result = $this->gestorParticipantes->Borra($IdParticipante);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el participante.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Participante borrado exitosamente.', 200);
    }
}
