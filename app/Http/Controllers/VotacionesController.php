<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use DB;
use Illuminate\Http\Request;

class VotacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listar(Request $request)
    {
        $pIdEvento = intval($request->input('pIdEvento')); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_votos(?)', [$pIdEvento]);
            Log:info($lista);
            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los votos.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function alta(Request $request)
    {

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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
