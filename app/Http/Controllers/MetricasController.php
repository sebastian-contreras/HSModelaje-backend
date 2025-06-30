<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreMetricaRequest;
use App\Http\Requests\UpdateMetricaRequest;
use App\Classes\Metricas;
use App\Services\GestorMetricas;
use Illuminate\Http\Request;

class MetricasController extends Controller
{
    protected $gestorMetricas;

    public function __construct(GestorMetricas $gestorMetricas)
    {
        $this->gestorMetricas = $gestorMetricas;
    }

    public function index(Request $request)
    {
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N');
        $pIdEvento = intval($request->input('pIdEvento'));

        try {
            $lista = $this->gestorMetricas->Listar($pIdEvento, $pIncluyeBajas);
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function busqueda(Request $request)
    {
        $pIdEvento = $request->input('pIdEvento', null);
        $pMetrica = $request->input('pMetrica', null);
        $pEstado = $request->input('pEstado', null);
        $pPagina = $request->input('pPagina', 1);
        $pCantidad = $request->input('pCantidad', 10);
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            $lista = $this->gestorMetricas->Buscar($pIdEvento, $pMetrica, $pEstado, $pOffset, $pCantidad);
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows ]);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error al obtener las metricas: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreMetricaRequest $request)
    {
        $request->validated();
        $metrica = new Metricas($request->all());
        $result = $this->gestorMetricas->Alta($metrica);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Metrica creada exitosamente.', 201);
    }

    public function show(string $id)
    {
        try {
            $metrica = new Metricas(['IdMetrica' => $id]);
            $result = $metrica->Dame();
            return ResponseFormatter::success($result);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error al obtener la metrica.', 500);
        }
    }

    public function update(UpdateMetricaRequest $request, int $IdMetrica)
    {
        $request->validated();
        $data = $request->all();
        $data['IdMetrica'] = $IdMetrica;
        $metrica = new Metricas($data);
        $result = $this->gestorMetricas->Modifica($metrica);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Metrica modificada exitosamente.', 201);
    }

    public function destroy(int $IdMetrica)
    {
        $result = $this->gestorMetricas->Borra($IdMetrica);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error('Error al borrar la metrica.', 400);
        }
        return ResponseFormatter::success(null, 'Metrica borrada exitosamente.', 200);
    }

    public function darBaja(int $IdMetrica)
    {
        $metrica = new Metricas(['IdMetrica' => $IdMetrica]);
        $result = $metrica->DarBaja();

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success(null, 'Metrica dada de baja exitosamente.', 200);
    }

    public function activar(int $IdMetrica)
    {
        $metrica = new Metricas(['IdMetrica' => $IdMetrica]);
        $result = $metrica->Activar();

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success(null, 'Metrica activa exitosamente.', 200);
    }
}