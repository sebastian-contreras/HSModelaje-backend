<?php

namespace App\Services;

use App\Classes\Establecimientos;
use Illuminate\Support\Facades\DB;

class GestorEstablecimientos
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_establecimiento(?)', [$pIncluyeBajas]);
    }
    public function Buscar($pCadena, $pIncluyeInactivos, $pOffset, $pCantidad)
    {
        return DB::select('CALL bsp_buscar_establecimiento(?,?,?,?)', [$pCadena, $pIncluyeInactivos, $pOffset, $pCantidad]);

    }

    public function Alta(Establecimientos $establecimiento)
    {
        return DB::select('CALL bsp_alta_establecimiento(?, ?, ?)', [
            $establecimiento->Establecimiento,
            $establecimiento->Ubicacion,
            $establecimiento->Capacidad,
        ]);
    }


    public function Modifica(Establecimientos $establecimiento)
    {
        return DB::select('CALL bsp_modifica_establecimiento(?, ?, ?, ?)', [
            $establecimiento->IdEstablecimiento,
            $establecimiento->Establecimiento,
            $establecimiento->Ubicacion,
            $establecimiento->Capacidad,
        ]);
    }

    public function Borra($IdEstablecimiento)
    {
        return DB::select('CALL bsp_borra_establecimiento(?)', [$IdEstablecimiento]);
    }
}