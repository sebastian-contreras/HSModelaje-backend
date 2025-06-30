<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GestorInformes
{
    public function ListarVotos($IdEvento)
    {
        return DB::select('CALL bsp_listar_votos(?)', [$IdEvento]);
    }

    public function InformeZona($IdEvento)
    {
        return DB::select('CALL bsp_informe_evento_zona(?)', [$IdEvento]);
    }

    public function InformeEstado($IdEvento)
    {
        return DB::select('CALL bsp_informe_evento_estado(?)', [$IdEvento]);
    }

    public function InformeGastos($IdEvento)
    {
        return DB::select('CALL bsp_informe_evento_gastos(?)', [$IdEvento]);
    }

    public function InformePatrocinadores($IdEvento)
    {
        return DB::select('CALL bsp_informe_evento_patrocinadores(?)', [$IdEvento]);
    }
}