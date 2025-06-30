<?php

namespace App\Services;

use App\Classes\Votaciones;
use Illuminate\Support\Facades\DB;

class GestorVotaciones
{
    public function Listar($IdEvento)
    {
        return DB::select('CALL bsp_listar_votos(?)', [$IdEvento]);
    }

    public function AltaVoto(Votaciones $votacion)
    {
        return DB::select('CALL bsp_alta_voto(?, ?, ?, ?, ?)', [
            $votacion->IdParticipante,
            $votacion->IdJuez,
            $votacion->IdMetrica,
            $votacion->Nota,
            null
        ]);

    }

    public function DameVotacionParticipante($IdEvento)
    {
        return DB::select('CALL bsp_dame_votacion_participante(?)', [$IdEvento]);
    }

    public function Iniciar($IdEvento)
    {
        return DB::select('CALL bsp_iniciar_votacion(?)', [$IdEvento]);
    }

    public function Finalizar($IdEvento)
    {
        return DB::select('CALL bsp_finalizar_votacion(?)', [$IdEvento]);
    }

    public function IniciarParticipante($IdParticipante)
    {
        return DB::select('CALL bsp_iniciar_votacion_participante(?)', [$IdParticipante]);
    }

    public function DetenerParticipante($IdParticipante)
    {
        return DB::select('CALL bsp_detener_votacion_participante(?)', [$IdParticipante]);
    }

    public function ReiniciarParticipante($IdParticipante)
    {
        return DB::select('CALL bsp_reiniciar_votacion_participante(?)', [$IdParticipante]);
    }
}