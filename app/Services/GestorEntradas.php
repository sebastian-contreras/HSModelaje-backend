<?php

namespace App\Services;

use App\Classes\Entradas;
use Illuminate\Support\Facades\DB;

class GestorEntradas
{
    public function Listar($IdEvento)
    {
        return DB::select('CALL bsp_listar_entradas(?)', [$IdEvento]);
    }

    public function Buscar($pCadena, $pDNI, $pEstado, $pIdZona, $pIdEvento, $pOffset, $pCantidad)
    {
        return DB::select('CALL bsp_buscar_entrada(?,?,?,?,?,?,?)', [
            $pCadena, $pDNI, $pEstado, $pIdZona, $pIdEvento, $pOffset, $pCantidad
        ]);
    }

    public function AltaVendedor(Entradas $entrada)
    {
        return DB::select('CALL bsp_alta_entrada_vendedor(?, ?, ?, ?, ?, ?, ?)', [
            $entrada->IdZona,
            $entrada->ApelName,
            $entrada->DNI,
            $entrada->Correo,
            $entrada->Telefono,
            $entrada->Comprobante,
            $entrada->Cantidad,
        ]);
    }

    public function AltaPasarela(Entradas $entrada)
    {
        return DB::select('CALL bsp_alta_entrada(?, ?, ?, ?, ?, ?, ?)', [
            $entrada->IdZona,
            $entrada->ApelName,
            $entrada->DNI,
            $entrada->Correo,
            $entrada->Telefono,
            $entrada->Comprobante,
            $entrada->Cantidad,
        ]);
    }

    public function Modifica(Entradas $entrada)
    {
        return DB::select('CALL bsp_modifica_entrada(?, ?, ?, ?, ?, ?, ?,?)', [
            $entrada->IdEntrada,
            $entrada->IdZona,
            $entrada->ApelName,
            $entrada->DNI,
            $entrada->Correo,
            $entrada->Telefono,
            $entrada->Cantidad,
            $entrada->Comprobante,
        ]);
    }

    public function Borra($IdEntrada)
    {
        return DB::select('CALL bsp_borra_entrada(?)', [$IdEntrada]);
    }

 
}