<?php

namespace App\Services;

use App\Classes\Eventos;
use Illuminate\Support\Facades\DB;

class GestorEventos extends GestorBase
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_evento(?)', [$pIncluyeBajas]);
    }

    public function Buscar($pOffset, $pCantidad,$pCadena=null, $pEstado=null, $pIncluyeVotacion=null, $pFechaInicio=null, $pFechaFinal=null)
    {
        return DB::select('CALL bsp_buscar_evento(?,?,?,?,?,?,?)', [
            $pCadena, $pEstado, $pIncluyeVotacion, $pFechaInicio, $pFechaFinal, $pOffset, $pCantidad
        ]);
    }

    public function Alta($evento)
    {
        return DB::select('CALL bsp_alta_evento(?, ?, ?,?,?,?,?,?)', [
            $evento->Evento,
            $evento->FechaProbableInicio,
            $evento->FechaProbableFinal,
            $evento->Votacion,
            $evento->IdEstablecimiento,
            $evento->TitularCuenta,
            $evento->Alias,
            $evento->CBU,
        ]);
    }

    public function Modifica($evento)
    {
        return DB::select('CALL bsp_modifica_evento(?, ?, ?,?,?,?,?,?,?,?,?)', [
            $evento->IdEvento,
            $evento->Evento,
            $evento->FechaProbableInicio,
            $evento->FechaProbableFinal,
            $evento->Votacion,
            null,
            null,
            $evento->IdEstablecimiento,
            $evento->TitularCuenta,
            $evento->Alias,
            $evento->CBU,
        ]);
    }

    public function Borra($IdEvento)
    {
        return DB::select('CALL bsp_borra_evento(?)', [$IdEvento]);
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