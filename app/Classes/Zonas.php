<?php

namespace App\Classes;

use DB;

class Zonas
{
    public $IdZona;
    public $IdEvento;
    public $Zona;
    public $Capacidad;
    public $AccesoDisc;
    public $Precio;
    public $Detalle;

    public function __construct(array $data)
    {
        $this->IdZona = $data['IdZona'] ?? null;
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->Zona = $data['Zona'] ?? null;
        $this->Capacidad = $data['Capacidad'] ?? null;
        $this->AccesoDisc = $data['AccesoDisc'] ?? null;
        $this->Precio = $data['Precio'] ?? null;
        $this->Detalle = $data['Detalle'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_zona(?)', [$this->IdZona]);
    }

    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_zona(?)', [$this->IdZona]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_zona(?)', [$this->IdZona]);
    }
}