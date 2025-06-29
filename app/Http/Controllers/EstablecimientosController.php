<?php

namespace App\Http\Controllers;

use App\Classes\Establecimientos;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEstablecimientoRequest;
use App\Http\Requests\UpdateEstablecimientoRequest;
use App\Services\GestorEstablecimientos;
use DB;
use Illuminate\Http\Request;
use Log;
use function Laravel\Prompts\warning;

class EstablecimientosController extends Controller
{
    protected $gestorEstablecimientos;

    public function __construct(GestorEstablecimientos $gestorEstablecimientos)
    {
        $this->gestorEstablecimientos = $gestorEstablecimientos;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorEstablecimientos->Listar($pIncluyeBajas);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los establecimientos.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        // Obtener los parámetros de la solicitud
        $pCadena = $request->input('pCadena', ''); // Valor por defecto ''
        $pIncluyeInactivos = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'
        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorEstablecimientos->Buscar($pCadena, $pIncluyeInactivos, $pOffset, $pCantidad);
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows]);
        } catch (\Exception $e) {
            // Manejo de errores
            Log::info('Error al obtener los establecimientos: ' . $e->getMessage());
            return ResponseFormatter::error('Error al obtener los establecimientos: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEstablecimientoRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $establecimiento = new Establecimientos($request->all());
        $result = $this->gestorEstablecimientos->Alta($establecimiento);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        return ResponseFormatter::success($result, 'Establecimiento creado exitosamente.', 201);

    }
    /**
     * Display the specified resource.
     */
    public function dame(string $IdEstablecimiento)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        try {
            // Llamar al procedimiento almacenado
            $establecimiento = new Establecimientos(['IdEstablecimiento' => $IdEstablecimiento]);
            $lista = $establecimiento->Dame();
            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener el establecimiento.', 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEstablecimientoRequest $request, int $IdEstablecimiento)
    {
        //
        $request->validated();
        $data = $request->all();
        $data['IdEstablecimiento'] = $IdEstablecimiento;
        $establecimiento = new Establecimientos($data);
        $result = $this->gestorEstablecimientos->Modifica($establecimiento);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Establecimiento modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdEstablecimiento)
    {
        // Llamar al procedimiento almacenado
        $result = $this->gestorEstablecimientos->Borra($IdEstablecimiento);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el establecimiento.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Establecimiento borrado exitosamente.', 200);
    }


    public function darBaja(int $IdEstablecimiento)
    {
        $establecimiento = new Establecimientos(['IdEstablecimiento' => $IdEstablecimiento]);
        $result = $establecimiento->DarBaja();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Establecimiento dado de baja exitosamente.', 200);
    }

    public function activar(int $IdEstablecimiento)
    {
        // Llamar al procedimiento almacenado
        $establecimiento = new Establecimientos(['IdEstablecimiento' => $IdEstablecimiento]);
        $result = $establecimiento->Activar();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Establecimiento activo exitosamente.', 200);
    }


}
