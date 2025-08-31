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
        $this->EstadoEvento = $data['EstadoEvento'] ?? null;
    }

    public static function Dame($IdEvento)
    {
        $result = DB::select('CALL bsp_dame_evento(?)', [$IdEvento]);
        return isset($result[0]) ? new Eventos((array) $result[0]) : null;
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


    public function ListarVotos()
    {
        return DB::select('CALL bsp_listar_votos(?)', [$this->IdEvento]);
    }

    public function InformeZona()
    {
        return DB::select('CALL bsp_informe_evento_zona(?)', [$this->IdEvento]);
    }

    public function InformeEstado()
    {
        return DB::select('CALL bsp_informe_evento_estado(?)', [$this->IdEvento]);
    }

    public function InformeGastos()
    {
        return DB::select('CALL bsp_informe_evento_gastos(?)', [$this->IdEvento]);
    }

    public function InformePatrocinadores()
    {
        return DB::select('CALL bsp_informe_evento_patrocinadores(?)', [$this->IdEvento]);
    }
}