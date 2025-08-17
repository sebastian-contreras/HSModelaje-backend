<?php

namespace App\Http\Controllers;

use App\Classes\Eventos;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Services\GestorEventos;
use DB;
use Illuminate\Http\Request;
use Number;
use Ramsey\Uuid\Type\Integer;

class EventosController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $gestorEventos;

    public function __construct(GestorEventos $gestorEventos)
    {
        $this->gestorEventos = $gestorEventos;
    }

    public function dame(string $IdEvento)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        try {
            // Llamar al procedimiento almacenado
            $lista = Eventos::Dame($IdEvento);
            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener el evento.', 500);
        }
    }
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorEventos->Listar($pIncluyeBajas);

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
            $lista = $this->gestorEventos->Buscar($pOffset, $pCantidad,$pCadena, $pEstado, $pIncluyeVotacion, $pFechaInicio, $pFechaFinal, );
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows]);
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
             $request->validated();
        $evento = new Eventos($request->all());
        $result = $this->gestorEventos->Alta($evento);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

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
        $data = $request->all();
        $data['IdEvento'] = $IdEvento;
        $evento = new Eventos($data);
        $result = $this->gestorEventos->Modifica($evento);


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
        $result = $this->gestorEventos->Borra($IdEvento);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400,);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento borrado exitosamente.', 200);
    }


    public function darBaja(int $IdEvento)
    {
        // Llamar al procedimiento almacenado
         $evento = new Eventos(['IdEvento' => $IdEvento]);
        $result = $evento->DarBaja();

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
        $evento = new Eventos(['IdEvento' => $IdEvento]);
        $result = $evento->Activar();

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento activo exitosamente.', 200);
    }

    public function finalizar(int $IdEvento, Request $request)
    {
        $pFechaInicio = $request->input('pFechaInicio');
        $pFechaFinal = $request->input('pFechaFinal');
        $evento = new Eventos(['IdEvento' => $IdEvento]);
        $result = $evento->Finalizar($pFechaInicio, $pFechaFinal);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Evento finalizado exitosamente.', 200);
    }
}
