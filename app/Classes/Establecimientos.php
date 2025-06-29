<?php

namespace App\Classes;

use DB;

class Establecimientos
{
    public $IdEstablecimiento;
    public $Establecimiento;
    public $Ubicacion;
    public $Capacidad;
    public $EstadoEstablecimiento;

    public function __construct(array $data)
    {
        $this->IdEstablecimiento = $data['IdEstablecimiento'] ?? null;
        $this->Establecimiento = $data['Establecimiento'] ?? null;
        $this->Ubicacion = $data['Ubicacion'] ?? null;
        $this->Capacidad = $data['Capacidad'] ?? null;
        $this->EstadoEstablecimiento = $data['EstadoEstablecimiento'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_establecimiento(?)', [$this->IdEstablecimiento]);
    }
    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_establecimiento(?)', [$this->IdEstablecimiento]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_establecimiento(?)', [$this->IdEstablecimiento]);
    }

}