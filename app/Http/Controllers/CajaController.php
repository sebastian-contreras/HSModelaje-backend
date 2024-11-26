<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreCajaRequest;
use App\Models\Caja;
use App\Traits\FilterableTrait;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use FilterableTrait;

    public function index(Request $request)
    {
        //
        $cantidad = $request->input('cantidad', 10);
        $pagina = $request->input('pagina', 1);
        // Obtener filtros de la solicitud
        $filters = $request->only([
            'IdCaja',
            'NumeroCaja',
            'Tamaño',
            'Ubicacion',
            'Fila',
            'Columna',
            'Observaciones',
            'EstadoCaja'
        ]);

        // Construir la consulta
        $query = Caja::query();

        $this->applyFilters($query, $filters);
        $validSortFields = [
            'IdCaja',
            'NumeroCaja',
            'Tamaño',
            'Ubicacion',
            'Fila',
            'Columna',
            'Observaciones',
            'EstadoCaja'
        ];
        $validSortDirections = ['asc', 'desc']; // Puedes personalizar esto
        $this->applySorting($query, $request, $validSortFields, $validSortDirections,'IdCaja');
        // Paginación
        $cajas = $query->paginate($cantidad, ['*'], 'page', $pagina);
        return ResponseFormatter::success(
            [
                'data' => $cajas->items(),
                'actual' => $cajas->currentPage(),
                'cantidad' => $cajas->perPage(),
                'ultima_pagina' => $cajas->lastPage(),
                'ultimo_registro' => $cajas->total(),
            ]
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCajaRequest $request)
    {
            $data = $request->validated();
            $caja = Caja::create($data);
            return ResponseFormatter::success($caja, 'Caja creada con exito.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Caja $caja)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Caja $caja)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Caja $caja)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Caja $caja)
    {
        //
    }
}
