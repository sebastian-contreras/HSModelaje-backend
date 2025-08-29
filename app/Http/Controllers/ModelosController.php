<?php

namespace App\Http\Controllers;

use App\Classes\Modelos;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEstablecimientoRequest;
use App\Http\Requests\StoreModeloRequest;
use App\Http\Requests\UpdateEstablecimientoRequest;
use App\Http\Requests\UpdateModeloRequest;
use App\Services\GestorModelos;
use DB;
use Illuminate\Http\Request;

class ModelosController extends Controller
{
        protected $gestorModelos;

    public function __construct(GestorModelos $gestorModelos)
    {
        $this->gestorModelos = $gestorModelos;
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
            $lista = $this->gestorModelos->Listar($pIncluyeBajas);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los modelos.', 500);
        }
    }


    public function busqueda(Request $request)
    {
        $pDNI = $request->input('pDNI',null);
        $pApelName = $request->input('pApelName',null);
        $pFechaNacimientoMin = $request->input('pFechaNacimientoMin',null);
        $pFechaNacimientoMax = $request->input('pFechaNacimientoMax',null);
        $pSexo = $request->input('pSexo',null);
        $pEstado = $request->input('pEstado',null);
        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorModelos->Buscar( $pOffset, $pCantidad,$pDNI, $pApelName, $pFechaNacimientoMin, $pFechaNacimientoMax, $pSexo, $pEstado);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los modelos: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreModeloRequest $request)
    {
        //
        $request->validated();
        $modelo = new Modelos($request->all());
        $result = $this->gestorModelos->Alta($modelo);


        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Modelo creado exitosamente.', 201);

    }
    /**
     * Display the specified resource.
     */
    public function dame(string $id)
    {
        try {
            $modelo = new Modelos(['IdModelo' => $id]);
            $result = $modelo->Dame();
            return ResponseFormatter::success($result);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error al obtener el modelo.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(UpdateModeloRequest $request, int $IdModelo)
    {
        $request->validated();
        $data = $request->all();
        $data['IdModelo'] = $IdModelo;
        $modelo = new Modelos($data);
        $result = $this->gestorModelos->Modifica($modelo);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Modelo modificado exitosamente.', 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdModelo)
    {
        // Llamar al procedimiento almacenado
        $result = $this->gestorModelos->Borra($IdModelo);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Modelo borrado exitosamente.', 200);
    }


    public function darBaja(int $IdModelo)
    {
        // Llamar al procedimiento almacenado
        $modelo = new Modelos(['IdModelo' => $IdModelo]);
        $result = $modelo->DarBaja();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Modelo dado de baja exitosamente.', 200);
    }

    public function activar(int $IdModelo)
    {
        // Llamar al procedimiento almacenado
        $modelo = new Modelos(['IdModelo' => $IdModelo]);
        $result = $modelo->Activar();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Modelo activo exitosamente.', 200);
    }


}
