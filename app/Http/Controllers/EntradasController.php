<?php

namespace App\Http\Controllers;

use App\Classes\Entradas;
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
use App\Services\GestorEntradas;
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
    protected $gestorEntradas;

    public function __construct(GestorEntradas $gestorEntradas)
    {
        $this->gestorEntradas = $gestorEntradas;
    }
    public function index($IdEvento)
    {
        $IdEvento = intval($IdEvento);

        try {
            // Llamar al procedimiento almacenado
            $lista = $this->gestorEntradas->ListarEnEvento($IdEvento);

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
            $entrada = new Entradas(['Token' => $Token]);
            $lista = $entrada->DamePorToken();
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
            $lista = $this->gestorEntradas->BuscarEnEvento($pOffset, $pCantidad, $pIdEvento, $pCadena, $pDNI, $pEstado, $pIdZona);
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
        $request->validated();
        $comprobantePath = null;
        if ($request->hasFile('Archivo')) {
            $comprobantePath = $request->file('Archivo')->store('comprobantes', 'public');
        }
        $entrada = new Entradas(array_merge($request->all(), ['Comprobante' => $comprobantePath]));
        $result = $this->gestorEntradas->AltaVendedor($entrada);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        $entradaData = (new Entradas(['IdEntrada' => $result[0]->Id]))->Dame();
        SendEmailJob::dispatch($entradaData[0]->Correo, new EntradaPendienteMail($entradaData));

        return ResponseFormatter::success($result, 'Entrada creada exitosamente.', 201);
    }


    public function storePasarela(StoreEntradaPasarelaRequest $request)
    {
        $data = $request->validated();
        $comprobantePath = null;
        if ($request->hasFile('Archivo')) {
            $comprobantePath = $request->file('Archivo')->store('comprobantes', 'public');
        }
        $entrada = new Entradas(array_merge($request->all(), ['Comprobante' => $comprobantePath]));
        $result = $this->gestorEntradas->AltaPasarela($entrada);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        $entradaData = (new Entradas(['IdEntrada' => $result[0]->Id]))->Dame();
        SendEmailJob::dispatch($entradaData[0]->Correo, new EntradaPendienteMail($entradaData));

        return ResponseFormatter::success($result, 'Entrada creada exitosamente.', 201);
    }
    /**
     * Display the specified resource.
     */

    public function dame(string $id)
    {
        try {
            $entrada = new Entradas(['IdEntrada' => $id]);
            $result = $entrada->Dame();
            return ResponseFormatter::success($result);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Error al obtener la entrada.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntradaRequest $request, int $IdEntrada)
    {
        $request->validated();
        $data = $request->all();
        $data['IdEntrada'] = $IdEntrada;
        $entrada = new Entradas($data);
        $result = $this->gestorEntradas->Modifica($entrada);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
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
        $result = $this->gestorEntradas->Borra($IdEntrada);

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
        $entrada = new Entradas(['IdEntrada' => $IdEntrada]);
        $result = $entrada->Abonar($IdEntrada);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }

        $entradaData = (new Entradas(['IdEntrada' => $IdEntrada]))->Dame();
        SendEmailJob::dispatch($entradaData[0]->Correo, new EntradaAprobadaMail($entradaData));


        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Entrada abonada exitosamente.', 200);
    }


    public function usar(int $IdEntrada)
    {
        // Llamar al procedimiento almacenado
        $entrada = new Entradas(['IdEntrada' => $IdEntrada]);
        $result = $entrada->Usar($IdEntrada);

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
        $entrada = new Entradas(['IdEntrada' => $IdEntrada]);
        $result = $entrada->Rechazar($IdEntrada);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al rechazar la entrada.', 400);
        }


        $entradaData = (new Entradas(['IdEntrada' => $IdEntrada]))->Dame();
        SendEmailJob::dispatch($entradaData[0]->Correo, new EntradaRechazadaMail($entradaData));

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Entrada rechazada exitosamente.', 200);
    }



}
