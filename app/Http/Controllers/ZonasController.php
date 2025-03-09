<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreZonaRequest;
use App\Http\Requests\UpdateZonaRequest;
use DB;
use Illuminate\Http\Request;

class ZonasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'
        $pIdEvento = intval($request->input('pIdEvento')); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_zonas(?,?)', [$pIdEvento,$pIncluyeBajas]);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los zonas.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        $pIdEvento = $request->input('pIdEvento',null);
        $pZona = $request->input('pZona',null);
        $pAccesoDisc = $request->input('pAccesoDisc',null);
        $pEstado = $request->input('pEstado',null);
        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_buscar_zonas(?,?,?,?,?,?)', [$pIdEvento,$pZona, $pAccesoDisc,$pEstado, $pOffset, $pCantidad]);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los zonas: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreZonaRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_zona( ?, ?,?, ?, ?,?)', [
            $request->IdEvento,
            $request->Zona,
            $request->Capacidad,
            $request->AccesoDisc,
            $request->Precio,
            $request->Detalle,
        ]);


        return ResponseFormatter::success($result, 'Zona creado exitosamente.', 201);

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
    public function update(UpdateZonaRequest $request, int $IdZona)
    {
        //
        $request->validated();
        $result = DB::select('CALL bsp_modifica_zona(?, ?,?, ?, ?,?)', [
            $request->IdZona,
            $request->Zona,
            $request->Capacidad,
            $request->AccesoDisc,
            $request->Precio,
            $request->Detalle,
        ]);



        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Zona modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdZona)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_zona(?)', [$IdZona]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el zona.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Zona borrado exitosamente.', 200);
    }


    public function darBaja(int $IdZona)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_darbaja_zona(?)', [$IdZona]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Zona dado de baja exitosamente.', 200);
    }

    public function activar(int $IdZona)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_activar_zona(?)', [$IdZona]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Zona activo exitosamente.', 200);
    }


}
