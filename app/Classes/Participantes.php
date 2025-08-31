<?php

namespace App\Classes;

use DB;

class Participantes
{
    public $IdParticipante;
    public $IdEvento;
    public $IdModelo;
    public $Promotor;

    public function __construct(array $data)
    {
        $this->IdParticipante = $data['IdParticipante'] ?? null;
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->IdModelo = $data['IdModelo'] ?? null;
        $this->Promotor = $data['Promotor'] ?? null;
        $this->ActivoVotacion = $data['ActivoVotacion'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_participante(?)', [$this->IdParticipante]);
    }
}