<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Models\Persona;
use App\Traits\FilterableTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    use FilterableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cantidad = $request->input('cantidad', 10);
        $pagina = $request->input('pagina', 1);
        // Obtener filtros de la solicitud
        $filters = $request->only(['IdPersona', 'CUIT', 'Apellido', 'Nombre', 'Nacionalidad', 'Actividad', 'Domicilio', 'Email', 'Telefono', 'Movil', 'SituacionFiscal', 'FNacimiento', 'DNI', 'Alias', 'CodPostal', 'PEP', 'EstadoPersona']);

        // Construir la consulta
        $query = Persona::query();

        // // Aplicar filtros
        $this->applyFilters($query, $filters);
        $validSortFields = ['IdPersona', 'CUIT', 'Apellido', 'Nombre', 'Nacionalidad', 'Actividad', 'Domicilio', 'Email', 'Telefono', 'Movil', 'SituacionFiscal', 'FNacimiento', 'DNI', 'Alias', 'CodPostal', 'PEP', 'EstadoPersona']; // Puedes personalizar esto
        $validSortDirections = ['asc', 'desc']; // Puedes personalizar esto
        $this->applySorting($query, $request, $validSortFields, $validSortDirections);

        // // Aplicar ordenamiento

        // Paginación
        $personas = $query->paginate($cantidad, ['*'], 'page', $pagina);
        return ResponseFormatter::success(
            [
                'data' => $personas->items(),
                'actual' => $personas->currentPage(),
                'cantidad' => $personas->perPage(),
                'ultima_pagina' => $personas->lastPage(),
                'ultimo_registro' => $personas->total(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        // Validar y obtener los datos del request
        $data = $request->validated();

        // Crear una nueva instancia de Persona
        $persona = Persona::create($data);

        // Retornar una respuesta exitosa
        return ResponseFormatter::success($persona, 'Persona creada exitosamente', 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $persona = Persona::findOrFail($id);
            return ResponseFormatter::success($persona);
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Persona no encontrada', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error interno del servidor', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        // Validar y obtener los datos del request
        $data = $request->validated();

        // Actualizar el modelo con los nuevos datos
        $persona->update($data);

        // Retornar una respuesta exitosa
        return ResponseFormatter::success(['data' => $persona], 'Persona actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        // Eliminar el modelo
        $persona->delete();

        // Retornar una respuesta exitosa
        return ResponseFormatter::success([], 'Persona eliminada exitosamente', 204);
    }
}
