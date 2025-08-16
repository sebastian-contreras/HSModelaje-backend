<?php

namespace App\Services;

use App\Classes\Jueces;
use Illuminate\Support\Facades\DB;

class GestorJueces extends GestorBase
{
    public function ListarEnEvento($IdEvento, $pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_jueces(?,?)', [$IdEvento, $pIncluyeBajas]);
    }

    public function BuscarEnEvento($Offset, $Cantidad, $IdEvento, $DNI = null, $ApelName = null, $Estado = null)
    {
        return DB::select('CALL bsp_buscar_juez(?,?,?,?,?,?)', [$IdEvento, $DNI, $ApelName, $Estado, $Offset, $Cantidad]);
    }

    public function Alta($juez)
    {
        return DB::select('CALL bsp_alta_juez(?, ?, ?, ?, ?)', [
            $juez->IdEvento,
            $juez->DNI,
            $juez->ApelName,
            $juez->Correo,
            $juez->Telefono,
        ]);
    }

    public function Modifica($juez)
    {
        return DB::select('CALL bsp_modifica_juez(?, ?, ?, ?, ?)', [
            $juez->IdJuez,
            $juez->DNI,
            $juez->ApelName,
            $juez->Telefono,
            $juez->Correo,
        ]);
    }

    public function Borra($IdJuez)
    {
        return DB::select('CALL bsp_borra_juez(?)', [$IdJuez]);
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