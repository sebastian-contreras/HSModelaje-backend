<?php

namespace App\Services;

use App\Classes\Entradas;
use Illuminate\Support\Facades\DB;

class GestorEntradas extends GestorBase
{
    public function ListarEnEvento($IdEvento)
    {
        return DB::select('CALL bsp_listar_entradas(?)', [$IdEvento]);
    }

    public function BuscarEnEvento($pOffset, $pCantidad, $pIdEvento, $pCadena = null, $pDNI = null, $pEstado = null, $pIdZona = null)
    {
        return DB::select('CALL bsp_buscar_entrada(?,?,?,?,?,?,?)', [
            $pCadena,
            $pDNI,
            $pEstado,
            $pIdZona,
            $pIdEvento,
            $pOffset,
            $pCantidad
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

    public function Modifica($entrada)
    {
        return DB::select('CALL bsp_modifica_entrada(?, ?, ?, ?, ?, ?,?)', [
            $entrada->IdEntrada,
            $entrada->IdZona,
            $entrada->ApelName,
            $entrada->DNI,
            $entrada->Correo,
            $entrada->Telefono,
            $entrada->Comprobante,
        ]);
    }

    public function Borra($IdEntrada)
    {
        return DB::select('CALL bsp_borra_entrada(?)', [$IdEntrada]);
    }

    /**
     * @deprecated No implementado.
     */
    public function Alta($entidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
    /**
     * @deprecated No implementado.
     */
    public function Listar($IdEntidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
    /**
     * @deprecated No implementado.
     */
    public function Buscar($offset, $cantidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }

}