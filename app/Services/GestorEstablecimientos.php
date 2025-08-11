<?php

namespace App\Services;

use App\Classes\Establecimientos;
use Illuminate\Support\Facades\DB;

class GestorEstablecimientos extends GestorBase
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_establecimiento(?)', [$pIncluyeBajas]);
    }
    public function Buscar( $pOffset, $pCantidad,$pCadena = null, $pIncluyeInactivos=null)
    {
        return DB::select('CALL bsp_buscar_establecimiento(?,?,?,?)', [$pCadena, $pIncluyeInactivos, $pOffset, $pCantidad]);

    }

    public function Alta($establecimiento)
    {
        return DB::select('CALL bsp_alta_establecimiento(?, ?, ?)', [
            $establecimiento->Establecimiento,
            $establecimiento->Ubicacion,
            $establecimiento->Capacidad,
        ]);
    }


    public function Modifica($establecimiento)
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