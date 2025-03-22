<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEstablecimientoRequest;
use App\Http\Requests\StoreGastoRequest;
use App\Http\Requests\StoreModeloRequest;
use App\Http\Requests\UpdateEstablecimientoRequest;
use App\Http\Requests\UpdateGastoRequest;
use App\Http\Requests\UpdateModeloRequest;
use DB;
use Illuminate\Http\Request;

class GastosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($IdEvento)
    {
        $IdEvento = intval($IdEvento);

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_gastos(?)', [$IdEvento]);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los gastos del evento.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        $pIdEvento = intval($request->input('pIdEvento'));
        $pGasto = $request->input('pGasto',null);

        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_buscar_gastos(?,?,?,?)', [$pIdEvento, $pGasto,$pOffset, $pCantidad]);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los gastos: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGastoRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_gasto(?, ?, ?,?, ?)', [
            $request->IdEvento,
            $request->Gasto,
            $request->Personal,
            $request->Monto,
            $request->Comprobante,
        ]);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        return ResponseFormatter::success($result, 'Gasto creado exitosamente.', 201);

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
    public function update(UpdateGastoRequest $request, int $IdGasto)
    {
        //
        $request->validated();
        $result = DB::select('CALL bsp_modifica_gasto(?,?, ?, ?,?)', [
            $request->IdGasto,
            $request->Gasto,
            $request->Personal,
            $request->Monto,
            $request->Comprobante,
        ]);


        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Gasto modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdGasto)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_gasto(?)', [$IdGasto]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el gasto.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Gasto borrado exitosamente.', 200);
    }




}
