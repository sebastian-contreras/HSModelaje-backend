<?php

namespace App\Services;

use App\Classes\Zonas;
use Illuminate\Support\Facades\DB;

class GestorZonas
{
    public function Listar($IdEvento, $pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_zonas(?,?)', [$IdEvento, $pIncluyeBajas]);
    }

    public function Buscar($IdEvento, $Zona, $AccesoDisc, $Estado, $Offset, $Cantidad)
    {
        return DB::select('CALL bsp_buscar_zonas(?,?,?,?,?,?)', [$IdEvento, $Zona, $AccesoDisc, $Estado, $Offset, $Cantidad]);
    }

    public function Alta(Zonas $zona)
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

    public function Modifica(Zonas $zona)
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