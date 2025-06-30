<?php

namespace App\Services;

use App\Classes\Participantes;
use Illuminate\Support\Facades\DB;

class GestorParticipantes
{
    public function Listar($IdEvento, $Offset, $Cantidad)
    {
        return DB::select('CALL bsp_listar_participantes(?,?,?)', [$IdEvento, $Offset, $Cantidad]);
    }

    public function Alta(Participantes $participante)
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
}