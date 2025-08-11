<?php

namespace App\Services;

use App\Classes\Zonas;
use Illuminate\Support\Facades\DB;

class GestorZonas extends GestorBaseEvento
{
    public function Listar($IdEvento, $pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_zonas(?,?)', [$IdEvento, $pIncluyeBajas]);
    }

    public function Buscar($Offset, $Cantidad, $IdEvento, $Zona = null,$Estado = null,$AccesoDisc = null)
    {
        return DB::select('CALL bsp_buscar_zonas(?,?,?,?,?,?)', [$IdEvento, $Zona, $AccesoDisc, $Estado, $Offset, $Cantidad]);
    }

    public function Alta($zona)
    {
        return DB::select('CALL bsp_alta_zona(?, ?, ?, ?, ?, ?)', [
            $zona->IdEvento,
            $zona->Zona,
            $zona->Capacidad,
            $zona->AccesoDisc,
            $zona->Precio,
            $zona->Detalle,
        ]);
    }

    public function Modifica($zona)
    {
        return DB::select('CALL bsp_modifica_zona(?, ?, ?, ?, ?, ?)', [
            $zona->IdZona,
            $zona->Zona,
            $zona->Capacidad,
            $zona->AccesoDisc,
            $zona->Precio,
            $zona->Detalle,
        ]);
    }

    public function Borra($IdZona)
    {
        return DB::select('CALL bsp_borra_zona(?)', [$IdZona]);
    }
}