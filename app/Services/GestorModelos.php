<?php

namespace App\Services;

use App\Classes\Modelos;
use Illuminate\Support\Facades\DB;

class GestorModelos extends GestorBase
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_modelos(?)', [$pIncluyeBajas]);
    }

    public function Buscar($pOffset, $pCantidad, $pDNI = null, $pApelName = null, $pFechaNacimientoMin = null, $pFechaNacimientoMax = null, $pSexo = null, $pEstado = null)
    {
        return DB::select('CALL bsp_buscar_modelo(?,?,?,?,?,?,?,?)', [
            $pDNI,
            $pApelName,
            $pFechaNacimientoMin,
            $pFechaNacimientoMax,
            $pSexo,
            $pEstado,
            $pOffset,
            $pCantidad
        ]);
    }

    public function Alta($modelo)
    {
        return DB::select('CALL bsp_alta_modelo(?, ?, ?, ?, ?, ?)', [
            $modelo->DNI,
            $modelo->ApelName,
            $modelo->FechaNacimiento,
            $modelo->Sexo,
            $modelo->Telefono,
            $modelo->Correo,
        ]);
    }

    public function Modifica($modelo)
    {
        return DB::select('CALL bsp_modifica_modelo(?, ?, ?, ?, ?, ?, ?)', [
            $modelo->IdModelo,
            $modelo->DNI,
            $modelo->ApelName,
            $modelo->FechaNacimiento,
            $modelo->Sexo,
            $modelo->Telefono,
            $modelo->Correo,
        ]);
    }

    public function Borra($IdModelo)
    {
        return DB::select('CALL bsp_borra_modelo(?)', [$IdModelo]);
    }
    /**
     * @deprecated No implementado.
     */
    public function ListarEnEvento($IdEntidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
    /**
     * @deprecated No implementado.
     */
    public function BuscarEnEvento($offset, $cantidad, $idEvento)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
}