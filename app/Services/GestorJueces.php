<?php

namespace App\Services;

use App\Classes\Jueces;
use Illuminate\Support\Facades\DB;

class GestorJueces extends GestorBaseEvento
{
    public function Listar($IdEvento, $pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_jueces(?,?)', [$IdEvento, $pIncluyeBajas]);
    }

    public function Buscar($Offset, $Cantidad, $IdEvento, $DNI = null, $ApelName = null, $Estado = null)
    {
        return DB::select('CALL bsp_buscar_juez(?,?,?,?,?,?)', [$IdEvento, $DNI, $ApelName, $Estado, $Offset, $Cantidad]);
    }

    public function Alta($juez)
    {
        return DB::select('CALL bsp_alta_juez(?, ?, ?, ?, ?)', [
            $juez->IdEvento,
            $juez->DNI,
            $juez->ApelName,
            $juez->Correo,
            $juez->Telefono,
        ]);
    }

    public function Modifica($juez)
    {
        return DB::select('CALL bsp_modifica_juez(?, ?, ?, ?, ?)', [
            $juez->IdJuez,
            $juez->DNI,
            $juez->ApelName,
            $juez->Telefono,
            $juez->Correo,
        ]);
    }

    public function Borra($IdJuez)
    {
        return DB::select('CALL bsp_borra_juez(?)', [$IdJuez]);
    }
}