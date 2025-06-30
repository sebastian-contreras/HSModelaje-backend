<?php

namespace App\Services;

use App\Classes\Modelos;
use Illuminate\Support\Facades\DB;

class GestorModelos
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_modelos(?)', [$pIncluyeBajas]);
    }

    public function Buscar($pDNI, $pApelName, $pFechaNacimientoMin, $pFechaNacimientoMax, $pSexo, $pEstado, $pOffset, $pCantidad)
    {
        return DB::select('CALL bsp_buscar_modelo(?,?,?,?,?,?,?,?)', [
            $pDNI, $pApelName, $pFechaNacimientoMin, $pFechaNacimientoMax, $pSexo, $pEstado, $pOffset, $pCantidad
        ]);
    }

    public function Alta(Modelos $modelo)
    {
        return DB::select('CALL bsp_alta_modelo(?, ?, ?, ?, ?, ?)', [
            $modelo->DNI,
            $modelo->ApelName,
            $modelo->FechaNacimiento,
            $modelo->Sexo,
            $modelo->Telefono,
            $modelo->Correo,
        ]);
    }

    public function Modifica(Modelos $modelo)
    {
        return DB::select('CALL bsp_modifica_modelo(?, ?, ?, ?, ?, ?, ?)', [
            $modelo->IdModelo,
            $modelo->DNI,
            $modelo->ApelName,
            $modelo->FechaNacimiento,
            $modelo->Sexo,
            $modelo->Telefono,
            $modelo->Correo,
        ]);
    }

    public function Borra($IdModelo)
    {
        return DB::select('CALL bsp_borra_modelo(?)', [$IdModelo]);
    }
}