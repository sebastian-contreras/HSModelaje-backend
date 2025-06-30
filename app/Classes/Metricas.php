<?php

namespace App\Classes;

use DB;

class Metricas
{
    public $IdMetrica;
    public $IdEvento;
    public $Metrica;
    public $Estado;

    public function __construct(array $data)
    {
        $this->IdMetrica = $data['IdMetrica'] ?? null;
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->Metrica = $data['Metrica'] ?? null;
        $this->Estado = $data['Estado'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_metrica(?)', [$this->IdMetrica]);
    }

    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_metrica(?)', [$this->IdMetrica]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_metrica(?)', [$this->IdMetrica]);
    }
}