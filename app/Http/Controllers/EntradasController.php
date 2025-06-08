<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreEntradaPasarelaRequest;
use App\Http\Requests\StoreEntradaRequest;
use App\Http\Requests\StoreEstablecimientoRequest;
use App\Http\Requests\StoreGastoRequest;
use App\Http\Requests\StoreModeloRequest;
use App\Http\Requests\UpdateEstablecimientoRequest;
use App\Http\Requests\UpdateGastoRequest;
use App\Http\Requests\UpdateModeloRequest;
use App\Http\Requests\UpdateEntradaRequest;
use App\Jobs\SendEmailJob;
use App\Mail\EntradaAprobadaMail;
use App\Mail\EntradaPendienteMail;
use App\Mail\EntradaRechazadaMail;
use DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Mail;

class EntradasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($IdEvento)
    {
        $IdEvento = intval($IdEvento);

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_listar_entradas(?)', [$IdEvento]);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los entradas del evento.', 500);
        }
    }

    public function dameToken(Request $request)
    {
        $Token = $request->input('pToken');
        Log::alert('$Token');
        Log::alert($Token);
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_dame_entrada_token(?)', [$Token]);
            if (isset($lista[0]->Response) && $lista[0]->Response === 'error') {
                // Si hay un error, devolver un error formateado
                return ResponseFormatter::error('Error al recuperar la entrada.', 400);
            }

            // Devolver el resultado como JSON
            return ResponseFormatter::success($lista);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener entrada.', 500);
        }
    }
    public function busqueda(Request $request)
    {
        $pCadena = $request->input('pCadena', null);
        $pDNI = $request->input('pDNI', null);
        $pEstado = $request->input('pEstado', null);
        $pIdZona = $request->input('pIdZona', null);
        $pIdEvento = $request->input('pIdEvento', null);

        $pPagina = $request->input('pPagina', 1); // Valor por defecto 1
        $pCantidad = $request->input('pCantidad', 10); // Valor por defecto 10

        // Calcular el offset
        $pOffset = ($pPagina - 1) * $pCantidad;

        try {
            // Llamar al procedimiento almacenado
            $lista = DB::select('CALL bsp_buscar_entrada(?,?,?,?,?,?,?)', [$pCadena, $pDNI, $pEstado, $pIdZona, $pIdEvento, $pOffset, $pCantidad]);
            // Verificar si hay resultados y calcular la cantidad total de páginas
            $totalRows = isset($lista[0]->TotalRows) ? $lista[0]->TotalRows : 0;
            $totalPaginas = $totalRows > 0 ? ceil($totalRows / $pCantidad) : 1;

            // Devolver el resultado como JSON
            return ResponseFormatter::success(['data' => $lista, 'total_pagina' => $totalPaginas, 'total_row' => $totalRows]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('Error al obtener los entradas: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntradaRequest $request)
    {
        //
        $request->validated();

        $comprobantePath = null;
        if ($request->hasFile('Archivo')) {
            $comprobantePath = $request->file('Archivo')->store('comprobantes', 'public'); // Guarda en storage/app/public/comprobantes
        }

        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_entrada_vendedor(?, ?, ?,?, ?,?,?)', [
            $request->IdZona,
            $request->ApelName,
            $request->DNI,
            $request->Correo,
            $request->Telefono,
            $comprobantePath,
            $request->Cantidad,

        ]);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }


        $entrada = DB::select('CALL bsp_dame_entrada(?)', [$result[0]->Id]);

        SendEmailJob::dispatch($entrada[0]->Correo, new EntradaPendienteMail($entrada));


        return ResponseFormatter::success($result, 'Entrada creada exitosamente.', 201);

    }

    public function storePasarela(StoreEntradaPasarelaRequest $request)
    {
        //
        $data = $request->validated();

        // Guardar el archivo si se proporciona
        $comprobantePath = null;
        if ($request->hasFile('Archivo')) {
            $comprobantePath = $request->file('Archivo')->store('comprobantes', 'public'); // Guarda en storage/app/public/comprobantes
        }

        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_entrada(?, ?, ?,?, ?,?,?)', [
            $request->IdZona,
            $request->ApelName,
            $request->DNI,
            $request->Correo,
            $request->Telefono,
            $comprobantePath,
            $request->Cantidad,

        ]);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        $entrada = DB::select('CALL bsp_dame_entrada(?)', [$result[0]->Id]);

        SendEmailJob::dispatch($entrada[0]->Correo, new EntradaPendienteMail($entrada));

        return ResponseFormatter::success($result, 'Entrada creada exitosamente.', 201);

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
    public function update(UpdateEntradaRequest $request, int $IdEntrada)
    {
        //
        $request->validated();
        $result = DB::select('CALL bsp_modifica_entrada(?,?, ?, ?,?,?,?)', [
            $request->IdEntrada,
            $request->IdZona,
            $request->ApelName,
            $request->DNI,
            $request->Correo,
            $request->Telefono,
            $request->Comprobante,
        ]);


        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Entrada modificada exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdEntrada)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_entrada(?)', [$IdEntrada]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar la entrada.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Entrada borrada exitosamente.', 200);
    }


    public function abonar(int $IdEntrada)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_abonar_entrada(?)', [$IdEntrada]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al abonar la entrada.', 400);
        }

        $entrada = DB::select('CALL bsp_dame_entrada(?)', [$IdEntrada]);
        SendEmailJob::dispatch($entrada[0]->Correo, new EntradaAprobadaMail($entrada));



        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Entrada abonada exitosamente.', 200);
    }


    public function usar(int $IdEntrada)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_usar_entrada(?)', [$IdEntrada]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al usar la entrada.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Entrada usada exitosamente.', 200);
    }

    public function rechazar(int $IdEntrada)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_rechazar_entrada(?)', [$IdEntrada]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al rechazar la entrada.', 400);
        }


        $entrada = DB::select('CALL bsp_dame_entrada(?)', [$IdEntrada]);

        SendEmailJob::dispatch($entrada[0]->Correo, new EntradaRechazadaMail($entrada));


        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Entrada rechazada exitosamente.', 200);
    }



}
