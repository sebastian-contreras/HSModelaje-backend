<?php

namespace App\Services;

use App\Classes\Metricas;
use Illuminate\Support\Facades\DB;

class GestorMetricas
{
    public function Listar($IdEvento, $pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_metricas(?,?)', [$IdEvento, $pIncluyeBajas]);
    }

    public function Buscar($IdEvento, $Metrica, $Estado, $Offset, $Cantidad)
    {
        return DB::select('CALL bsp_buscar_metricas(?,?,?,?,?)', [$IdEvento, $Metrica, $Estado, $Offset, $Cantidad]);
    }

    public function Alta(Metricas $metrica)
    {
        return DB::select('CALL bsp_alta_metrica(?, ?)', [
            $metrica->IdEvento,
            $metrica->Metrica,
        ]);
    }

    public function Modifica(Metricas $metrica)
    {
        return DB::select('CALL bsp_modifica_metrica(?, ?)', [
            $metrica->IdMetrica,
            $metrica->Metrica,
        ]);
    }

    public function Borra($IdMetrica)
    {
        return DB::select('CALL bsp_borra_metrica(?)', [$IdMetrica]);
    }
}