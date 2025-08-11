<?php

namespace App\Services;

use App\Classes\Metricas;
use Illuminate\Support\Facades\DB;

class GestorMetricas extends GestorBaseEvento
{
    public function Listar($IdEvento, $pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_metricas(?,?)', [$IdEvento, $pIncluyeBajas]);
    }

    public function Buscar($Offset, $Cantidad, $IdEvento, $Metrica = null,$pIncluyeInactivos='N')
    {
        return DB::select('CALL bsp_buscar_metricas(?,?,?,?,?)', [$IdEvento, $Metrica, $pIncluyeInactivos, $Offset, $Cantidad]);
    }

    public function Alta($metrica)
    {
        return DB::select('CALL bsp_alta_metrica(?, ?)', [
            $metrica->IdEvento,
            $metrica->Metrica,
        ]);
    }

    public function Modifica($metrica)
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