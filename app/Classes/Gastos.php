<?php

namespace App\Classes;

use DB;

class Gastos
{
    public $IdGasto;
    public $IdEvento;
    public $Gasto;
    public $Personal;
    public $Monto;
    public $Comprobante;

    public function __construct(array $data)
    {
        $this->IdGasto = $data['IdGasto'] ?? null;
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->Gasto = $data['Gasto'] ?? null;
        $this->Personal = $data['Personal'] ?? null;
        $this->Monto = $data['Monto'] ?? null;
        $this->Comprobante = $data['Comprobante'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_gasto(?)', [$this->IdGasto]);
    }
}