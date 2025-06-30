<?php

namespace App\Classes;

use DB;

class Jueces
{
    public $IdJuez;
    public $IdEvento;
    public $DNI;
    public $ApelName;
    public $Correo;
    public $Telefono;
    public $Token;
    public $Estado;

    public function __construct(array $data)
    {
        $this->IdJuez = $data['IdJuez'] ?? null;
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->DNI = $data['DNI'] ?? null;
        $this->ApelName = $data['ApelName'] ?? null;
        $this->Correo = $data['Correo'] ?? null;
        $this->Telefono = $data['Telefono'] ?? null;
        $this->Token = $data['Token'] ?? null;
        $this->Estado = $data['Estado'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_juez(?)', [$this->IdJuez]);
    }

    public function DamePorToken()
    {
        return DB::select('CALL bsp_dame_juez_token(?)', [$this->Token]);
    }

    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_juez(?)', [$this->IdJuez]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_juez(?)', [$this->IdJuez]);
    }
}