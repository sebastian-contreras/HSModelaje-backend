<?php

namespace App\Services;

use App\Classes\Eventos;
use Illuminate\Support\Facades\DB;

class GestorEventos
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_evento(?)', [$pIncluyeBajas]);
    }

    public function Buscar($pCadena, $pEstado, $pIncluyeVotacion, $pFechaInicio, $pFechaFinal, $pOffset, $pCantidad)
    {
        return DB::select('CALL bsp_buscar_evento(?,?,?,?,?,?,?)', [
            $pCadena, $pEstado, $pIncluyeVotacion, $pFechaInicio, $pFechaFinal, $pOffset, $pCantidad
        ]);
    }

    public function Alta(Eventos $evento)
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

    public function Modifica(Eventos $evento)
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
}