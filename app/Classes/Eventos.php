<?php

namespace App\Classes;

use DB;

class Eventos
{
    public $IdEvento;
    public $Evento;
    public $FechaProbableInicio;
    public $FechaProbableFinal;
    public $Votacion;
    public $IdEstablecimiento;
    public $TitularCuenta;
    public $Alias;
    public $CBU;

    public function __construct(array $data)
    {
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->Evento = $data['Evento'] ?? null;
        $this->FechaProbableInicio = $data['FechaProbableInicio'] ?? null;
        $this->FechaProbableFinal = $data['FechaProbableFinal'] ?? null;
        $this->Votacion = $data['Votacion'] ?? null;
        $this->IdEstablecimiento = $data['IdEstablecimiento'] ?? null;
        $this->TitularCuenta = $data['TitularCuenta'] ?? null;
        $this->Alias = $data['Alias'] ?? null;
        $this->CBU = $data['CBU'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_evento(?)', [$this->IdEvento]);
    }

    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_evento(?)', [$this->IdEvento]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_evento(?)', [$this->IdEvento]);
    }

    public function Finalizar($fechaInicio, $fechaFinal)
    {
        return DB::select('CALL bsp_finalizar_evento(?,?,?)', [$this->IdEvento, $fechaInicio, $fechaFinal]);
    }
}