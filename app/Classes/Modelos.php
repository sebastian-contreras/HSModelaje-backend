<?php

namespace App\Classes;

use DB;

class Modelos
{
    public $IdModelo;
    public $DNI;
    public $ApelName;
    public $FechaNacimiento;
    public $Sexo;
    public $Telefono;
    public $Correo;

    public function __construct(array $data)
    {
        $this->IdModelo = $data['IdModelo'] ?? null;
        $this->DNI = $data['DNI'] ?? null;
        $this->ApelName = $data['ApelName'] ?? null;
        $this->FechaNacimiento = $data['FechaNacimiento'] ?? null;
        $this->Sexo = $data['Sexo'] ?? null;
        $this->Telefono = $data['Telefono'] ?? null;
        $this->Correo = $data['Correo'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_modelo(?)', [$this->IdModelo]);
    }

    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_modelo(?)', [$this->IdModelo]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_modelo(?)', [$this->IdModelo]);
    }
}