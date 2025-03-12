<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEstablecimientoRequest;
use App\Http\Requests\StoreGastoRequest;
use App\Http\Requests\StoreModeloRequest;
use App\Http\Requests\StorePatrocinadorRequest;
use App\Http\Requests\UpdateEstablecimientoRequest;
use App\Http\Requests\UpdateGastoRequest;
use App\Http\Requests\UpdateModeloRequest;
use App\Http\Requests\UpdatePatrocinadorRequest;
use DB;
use Illuminate\Http\Request;

class PatrocinadoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($IdEvento)
    {
        $IdEvento = intval($IdEvento);

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_patrocinadores(?)', [$IdEvento]);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los patrocinadores del evento.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        $pIdEvento = intval($request->input('pIdEvento'));
        $pPatrocinador = $request->input('pPatrocinador', null);

        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_buscar_patrocinadores(?,?,?,?)', [$pIdEvento, $pPatrocinador, $pOffset, $pCantidad]);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los patrocinadores: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatrocinadorRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_patrocinador(?, ?, ?,?, ?,?)', [
            $request->IdEvento,
            $request->Patrocinador,
            $request->Correo,
            $request->Telefono,
            $request->DomicilioRef,
            $request->Descripcion,
        ]);


        return ResponseFormatter::success($result, 'Patrocinador creado exitosamente.', 201);

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
    public function update(UpdatePatrocinadorRequest $request, int $IdPatrocinador)
    {
        //
        $request->validated();
        $result = DB::select('CALL bsp_modifica_patrocinador(?,?, ?, ?,?,?)', [
            $request->IdPatrocinador,
            $request->Patrocinador,
            $request->Correo,
            $request->Telefono,
            $request->DomicilioRef,
            $request->Descripcion,
        ]);


        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Patrocinador modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdPatrocinador)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_patrocinador(?)', [$IdPatrocinador]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el patrocinador.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Patrocinador borrado exitosamente.', 200);
    }




}
