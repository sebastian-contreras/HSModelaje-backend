<?php

namespace App\Services;

use App\Classes\Participantes;
use Illuminate\Support\Facades\DB;

class GestorParticipantes extends GestorBaseEvento
{
    public function Listar($IdEvento,$Offset=0, $Cantidad=10 )
    {
        return DB::select('CALL bsp_listar_participantes(?,?,?)', [$IdEvento, $Offset, $Cantidad]);
    }

    public function Alta($participante)
    {
        return DB::select('CALL bsp_alta_participante(?, ?, ?)', [
            $participante->IdEvento,
            $participante->IdModelo,
            $participante->Promotor,
        ]);
    }

    public function Borra($IdParticipante)
    {
        return DB::select('CALL bsp_borra_participante(?)', [$IdParticipante]);
    }
    /**
     * @deprecated No implementado para GestorParticipantes.
     */
    public function Buscar($offset, $cantidad, $idEvento)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado. Usa BuscarNuevo() en su lugar.',
            E_USER_DEPRECATED
        );
    }

    /**
     * @deprecated No implementado para GestorParticipantes.
     */
    public function Modifica($entidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
}