<?php

namespace App\Http\Controllers;

use App\Classes\Zonas;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreZonaRequest;
use App\Http\Requests\UpdateZonaRequest;
use App\Services\GestorZonas;
use DB;
use Illuminate\Http\Request;
use Log;

class ZonasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        protected $gestorZonas;

    public function __construct(GestorZonas $gestorZonas)
    {
        $this->gestorZonas = $gestorZonas;
    }
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'
        $pIdEvento = intval($request->input('pIdEvento')); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorZonas->Listar($pIdEvento, $pIncluyeBajas);

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
            $lista = $this->gestorZonas->Buscar($pOffset, $pCantidad, $pIdEvento, $pZona, $pEstado, AccesoDisc: $pAccesoDisc); 
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
        $zona = new Zonas($request->all());
        Log::info('Creando zona con datos: ', [$zona]);
        $result = $this->gestorZonas->Alta($zona);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        return ResponseFormatter::success($result, 'Zona creado exitosamente.', 201);

    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $zona = new Zonas(['IdZona' => $id]);
            $result = $zona->Dame();
            return ResponseFormatter::success($result);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error al obtener la zona.', 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateZonaRequest $request, int $IdZona)
    {
        $request->validated();
        $data = $request->all();
        $data['IdZona'] = $IdZona;
        $zona = new Zonas($data);
        $result = $this->gestorZonas->Modifica($zona);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Zona modificado exitosamente.', 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdZona)
    {
        $result = $this->gestorZonas->Borra($IdZona);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error('Error al borrar la zona.', 400);
        }
        return ResponseFormatter::success(null, 'Zona borrado exitosamente.', 200);
    }

  public function darBaja(int $IdZona)
    {
        $zona = new Zonas(['IdZona' => $IdZona]);
        $result = $zona->DarBaja();

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success(null, 'Zona dado de baja exitosamente.', 200);
    }

    public function activar(int $IdZona)
    {
        $zona = new Zonas(['IdZona' => $IdZona]);
        $result = $zona->Activar();

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success(null, 'Zona activo exitosamente.', 200);
    }

}
