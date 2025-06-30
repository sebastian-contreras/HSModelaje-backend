<?php

namespace App\Classes;

use DB;

class Votaciones
{
    public $IdParticipante;
    public $IdJuez;
    public $IdMetrica;
    public $Nota;

    public function __construct(array $data)
    {
        $this->IdParticipante = $data['IdParticipante'] ?? null;
        $this->IdJuez = $data['IdJuez'] ?? null;
        $this->IdMetrica = $data['IdMetrica'] ?? null;
        $this->Nota = $data['Nota'] ?? null;
    }
}