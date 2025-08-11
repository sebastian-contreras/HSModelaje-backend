<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class GestorGastos extends GestorBaseEvento
{
    public function Listar($IdEvento)
    {
        return DB::select('CALL bsp_listar_gastos(?)', [$IdEvento]);
    }

    public function Buscar($Offset, $Cantidad, $IdEvento, $Gasto = null)
    {
        return DB::select('CALL bsp_buscar_gastos(?,?,?,?)', [$IdEvento, $Gasto, $Offset, $Cantidad]);
    }

    public function Alta($gasto)
    {
        return DB::select('CALL bsp_alta_gasto(?, ?, ?, ?, ?)', [
            $gasto->IdEvento,
            $gasto->Gasto,
            $gasto->Personal,
            $gasto->Monto,
            $gasto->Comprobante,
        ]);
    }

    public function Modifica($gasto)
    {

        return DB::select('CALL bsp_modifica_gasto(?, ?, ?, ?, ?)', [
            $gasto->IdGasto,
            $gasto->Gasto,
            $gasto->Personal,
            $gasto->Monto,
            $gasto->Comprobante,
        ]);
    }

    public function Borra($IdGasto)
    {
        return DB::select('CALL bsp_borra_gasto(?)', [$IdGasto]);
    }
}